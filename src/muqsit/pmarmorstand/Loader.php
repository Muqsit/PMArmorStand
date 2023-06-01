<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand;

use muqsit\pmarmorstand\behaviour\ArmorStandBehaviourRegistry;
use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandPoseEvent;
use muqsit\pmarmorstand\pose\ArmorStandPoseRegistry;
use muqsit\pmarmorstand\vanilla\ExtraVanillaData;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
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
		}, ["PMArmorStand"]);

		ExtraVanillaData::registerOnAllThreads($this->getServer()->getAsyncPool());
	}

	protected function onEnable() : void{
		SimplePacketHandler::createMonitor($this)->monitorIncoming(function(InventoryTransactionPacket $packet, NetworkSession $session) : void{
			$player = $session->getPlayer();
			if($player === null){
				return;
			}

			$trData = $packet->trData;
			if(!($trData instanceof UseItemOnEntityTransactionData) || $trData->getActionType() !== UseItemOnEntityTransactionData::ACTION_INTERACT){
				return;
			}

			$world = $player->getWorld();
			$entity = $world->getEntity($trData->getActorRuntimeId());
			if(!($entity instanceof ArmorStandEntity)){
				return;
			}

			$click_pos = $trData->getClickPosition();
			if(!$player->canInteract($click_pos, 8) || !$entity->boundingBox->expandedCopy(0.25, 0.25, 0.25)->isVectorInside($click_pos)){
				return;
			}

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
		});
	}

	public function getBehaviourRegistry() : ArmorStandBehaviourRegistry{
		return $this->behaviour_registry;
	}
}