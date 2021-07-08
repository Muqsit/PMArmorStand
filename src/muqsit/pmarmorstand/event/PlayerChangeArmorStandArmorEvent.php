<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\event;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class PlayerChangeArmorStandArmorEvent extends PlayerChangeArmorStandItemEvent{

	protected int $slot;

	public function __construct(ArmorStandEntity $entity, int $slot, Item $old_item, Item $new_item, Player $causer){
		parent::__construct($entity, $old_item, $new_item, $causer);
		$this->slot = $slot;
	}

	public function getSlot() : int{
		return $this->slot;
	}
}