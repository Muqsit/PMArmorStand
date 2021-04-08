<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\event;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;

/**
 * @phpstan-extends EntityEvent<ArmorStandEntity>
 */
abstract class PlayerChangeArmorStandItemEvent extends EntityEvent implements Cancellable{
	use CancellableTrait;

	protected Item $old_item;
	protected Item $new_item;
	protected Player $causer;

	public function __construct(ArmorStandEntity $entity, Item $old_item, Item $new_item, Player $causer){
		$this->entity = $entity;
		$this->old_item = $old_item;
		$this->new_item = $new_item;
		$this->causer = $causer;
	}

	public function getOldItem() : Item{
		return $this->old_item;
	}

	public function getNewItem() : Item{
		return $this->new_item;
	}

	public function getCauser() : Player{
		return $this->causer;
	}
}