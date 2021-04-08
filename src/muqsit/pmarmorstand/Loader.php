<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand;

use muqsit\pmarmorstand\behaviour\ArmorStandBehaviourRegistry;
use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandPoseEvent;
use muqsit\pmarmorstand\pose\ArmorStandPoseRegistry;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

final class Loader extends PluginBase{

	private ArmorStandBehaviourRegistry $behaviour_registry;

	protected function onLoad() : void{
		$this->behaviour_registry = new ArmorStandBehaviourRegistry();

		EntityFactory::getInstance()->register(ArmorStandEntity::class, function(World $world, CompoundTag $nbt) : ArmorStandEntity{
			return new ArmorStandEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ["PMArmorStand"], EntityLegacyIds::ARMOR_STAND);

		ItemFactory::getInstance()->register(new ArmorStandItem(new ItemIdentifier(ItemIds::ARMOR_STAND, 0), "Armor Stand"));
	}

	protected function onEnable() : void{
		SimplePacketHandler::createMonitor($this)->monitorIncoming(function(InventoryTransactionPacket $packet, NetworkSession $session) : void{
			$player = $session->getPlayer();
			if($player !== null){
				$trData = $packet->trData;
				if($trData instanceof UseItemOnEntityTransactionData && $trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT){
					$world = $player->getWorld();
					$entity = $world->getEntity($trData->getEntityRuntimeId());
					if($entity instanceof ArmorStandEntity){
						$click_pos = $trData->getClickPos();
						if($player->canInteract($click_pos, 8) && $entity->boundingBox->expandedCopy(0.25, 0.25, 0.25)->isVectorInside($click_pos)){
							if($player->isSneaking()){
								$old_pose = $entity->getPose();
								$new_pose = ArmorStandPoseRegistry::instance()->next($old_pose);
								$ev = new PlayerChangeArmorStandPoseEvent($entity, $old_pose, $new_pose, $player);
								$ev->call();
								if(!$ev->isCancelled()){
									$entity->setPose($ev->getNewPose());
								}
							}else{
								$this->behaviour_registry->get($player->getInventory()->getItemInHand())->handleEquipment($player, $entity, $click_pos);
							}
						}
					}
				}
			}
		});
	}

	public function getBehaviourRegistry() : ArmorStandBehaviourRegistry{
		return $this->behaviour_registry;
	}
}