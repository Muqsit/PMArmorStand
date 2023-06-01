<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\vanilla;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static ArmorStandItem ARMOR_STAND()
 */
final class ExtraVanillaItems{
	use CloningRegistryTrait;

	private function __construct(){
	}

	protected static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	/**
	 * @return Item[]
	 * @phpstan-return array<string, Item>
	 */
	public static function getAll() : array{
		/** @var Item[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup() : void{
		self::register("armor_stand", new ArmorStandItem(new ItemIdentifier(ItemTypeIds::newId()), "Armor Stand"));
	}
}