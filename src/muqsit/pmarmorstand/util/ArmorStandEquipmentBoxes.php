<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\util;

use pocketmine\math\AxisAlignedBB;

final class ArmorStandEquipmentBoxes{

	public static function HELMET() : AxisAlignedBB{
		return new AxisAlignedBB(-0.2, 1.6, -0.2, 0.2, 1.975, 0.2);
	}

	public static function CHESTPLATE() : AxisAlignedBB{
		return new AxisAlignedBB(-0.2, 0.9, -0.2, 0.2, 1.6, 0.2);
	}

	public static function LEGGINGS() : AxisAlignedBB{
		return new AxisAlignedBB(-0.2, 0.4, -0.2, 0.2, 0.9, 0.2);
	}

	public static function BOOTS() : AxisAlignedBB{
		return new AxisAlignedBB(-0.2, 0.1, -0.2, 0.2, 0.4, 0.2);
	}

	public static function RIGHT_ARM() : AxisAlignedBB{
		return new AxisAlignedBB(0.3, 0.8, -0.3, 0.45, 1.6, 0.3);
	}
}