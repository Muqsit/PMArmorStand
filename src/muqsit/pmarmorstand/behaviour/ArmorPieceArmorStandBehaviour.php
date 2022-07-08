<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\behaviour;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\event\PlayerChangeArmorStandArmorEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class ArmorPieceArmorStandBehaviour implements ArmorStandBehaviour{

	public function __construct(
		private int $slot
	){}

	public function handleEquipment(Player $player, ArmorStandEntity $entity, Vector3 $click_pos) : void{
		$inventory = $player->getInventory();
		$item = $player->getInventory()->getItemInHand();

		$entity_inventory = $entity->getArmorInventory();
		$old_item = $entity_inventory->getItem($this->slot);
		$new_item = $item->pop();

		$ev = new PlayerChangeArmorStandArmorEvent($entity, $this->slot, $old_item, $new_item, $player);
		$ev->call();
		if(!$ev->isCancelled()){
			$inventory->setItemInHand($item);
			foreach($inventory->addItem($old_item) as $dropped){
				$player->getWorld()->dropItem($player->getEyePos(), $dropped);
			}
			$entity_inventory->setItem($this->slot, $new_item);
		}
	}
}