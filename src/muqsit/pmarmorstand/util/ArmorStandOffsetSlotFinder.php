<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\util;

use Generator;
use pocketmine\inventory\ArmorInventory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

final class ArmorStandOffsetSlotFinder{

	/**
	 * @return Generator<int, AxisAlignedBB>
	 */
	public static function getArmorInventorySlotBoxes() : Generator{
		yield ArmorInventory::SLOT_HEAD => ArmorStandEquipmentBoxes::HELMET();
		yield ArmorInventory::SLOT_CHEST => ArmorStandEquipmentBoxes::CHESTPLATE();
		yield ArmorInventory::SLOT_LEGS => ArmorStandEquipmentBoxes::LEGGINGS();
		yield ArmorInventory::SLOT_FEET => ArmorStandEquipmentBoxes::BOOTS();
	}

	public static function isOffsetInsideBB(Vector3 $offset, AxisAlignedBB $bb) : bool{
		return $bb->isVectorInXY($offset) || $bb->isVectorInYZ($offset);
	}

	public static function findArmorInventorySlot(Vector3 $offset) : ?int{
		foreach(self::getArmorInventorySlotBoxes() as $slot => $bb){
			if(self::isOffsetInsideBB($offset, $bb)){
				return $slot;
			}
		}
		return null;
	}

	public static function isRightArm(Vector3 $offset) : bool{
		return self::isOffsetInsideBB($offset, ArmorStandEquipmentBoxes::RIGHT_ARM());
	}
}