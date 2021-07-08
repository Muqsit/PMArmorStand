<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\event;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityEvent;

/**
 * @phpstan-extends EntityEvent<ArmorStandEntity>
 */
final class ArmorStandMoveEvent extends EntityEvent{

	private Location $from;
	private Location $to;

	public function __construct(ArmorStandEntity $entity, Location $from, Location $to){
		$this->entity = $entity;
		$this->from = $from;
		$this->to = $to;
	}

	public function getFrom() : Location{
		return $this->from->asLocation();
	}

	public function getTo() : Location{
		return $this->to->asLocation();
	}
}