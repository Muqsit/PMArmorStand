<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\behaviour;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandHeldItemEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class HeldItemArmorStandBehaviour implements ArmorStandBehaviour{

	public function __construct(){
	}

	public function handleEquipment(Player $player, ArmorStandEntity $entity, Vector3 $click_pos) : void{
		$inventory = $player->getInventory();
		$item = $player->getInventory()->getItemInHand();

		$old_item = $entity->getItemInHand();
		$new_item = $item->pop();

		$ev = new PlayerChangeArmorStandHeldItemEvent($entity, $old_item, $new_item, $player);
		$ev->call();
		if(!$ev->isCancelled()){
			$inventory->setItemInHand($item);
			foreach($inventory->addItem($old_item) as $dropped){
				$player->getWorld()->dropItem($player->getEyePos(), $dropped);
			}
			$entity->setItemInHand($new_item);
		}
	}
}