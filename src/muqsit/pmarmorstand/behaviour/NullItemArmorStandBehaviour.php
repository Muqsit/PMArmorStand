<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\behaviour;

use InvalidArgumentException;
use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandArmorEvent;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandHeldItemEvent;
use muqsit\pmarmorstand\util\ArmorStandOffsetSlotFinder;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class NullItemArmorStandBehaviour implements ArmorStandBehaviour{

	public function __construct(){
	}

	public function handleEquipment(Player $player, ArmorStandEntity $entity, Vector3 $click_pos) : void{
		$inventory = $player->getInventory();
		$item = $player->getInventory()->getItemInHand();
		if(!$item->isNull()){
			throw new InvalidArgumentException(self::class . " does not accept item {$item}");
		}

		$offset = $click_pos->subtractVector($entity->getPosition());
		$new_item = VanillaItems::AIR(); // or, $item
		if(ArmorStandOffsetSlotFinder::isRightArm($offset)){
			$old_item = $entity->getItemInHand();
			if(!$old_item->isNull()){
				$ev = new PlayerChangeArmorStandHeldItemEvent($entity, $old_item, $new_item, $player);
				$ev->call();
				if(!$ev->isCancelled()){
					$inventory->setItemInHand($old_item);
					$entity->setItemInHand($new_item);
				}
			}
		}else{
			$armor_slot = ArmorStandOffsetSlotFinder::findArmorInventorySlot($offset/* TODO: divide offset by scale? */) ?? ArmorInventory::SLOT_HEAD;
			$entity_inventory = $entity->getArmorInventory();
			$old_item = $entity_inventory->getItem($armor_slot);
			if(!$old_item->isNull()){
				$ev = new PlayerChangeArmorStandArmorEvent($entity, $armor_slot, $old_item, $new_item, $player);
				$ev->call();
				if(!$ev->isCancelled()){
					$inventory->setItemInHand($old_item);
					$entity_inventory->setItem($armor_slot, $new_item);
				}
			}
		}
	}
}