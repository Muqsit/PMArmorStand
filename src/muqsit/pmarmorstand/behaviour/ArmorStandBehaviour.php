<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\behaviour;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

interface ArmorStandBehaviour{

	public function handleEquipment(Player $player, ArmorStandEntity $entity, Vector3 $click_pos) : void;
}