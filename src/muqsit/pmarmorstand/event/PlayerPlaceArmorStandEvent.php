<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\event;

use pocketmine\entity\Location;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

final class PlayerPlaceArmorStandEvent extends PlayerEvent implements Cancellable{
	use CancellableTrait;

	private Location $location;

	public function __construct(Player $player, Location $location){
		$this->player = $player;
		$this->location = $location;
	}

	public function getLocation() : Location{
		return $this->location->asLocation();
	}
}