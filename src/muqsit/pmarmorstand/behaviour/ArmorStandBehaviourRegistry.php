<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\behaviour;

use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

final class ArmorStandBehaviourRegistry{

	/** @var ArmorStandBehaviour[] */
	private array $behaviours = [];

	private ArmorStandBehaviour $fallback;

	public function __construct(){
		$this->registerFallback(new HeldItemArmorStandBehaviour());

		foreach(ItemFactory::getInstance()->getAllRegistered() as $item){
			if($item instanceof Armor){
				$this->register($item, new ArmorPieceArmorStandBehaviour($item->getArmorSlot()));
			}
		}

		$this->register(VanillaBlocks::MOB_HEAD()->asItem(), new ArmorPieceArmorStandBehaviour(ArmorInventory::SLOT_HEAD));
		$this->register(VanillaBlocks::CARVED_PUMPKIN()->asItem(), new ArmorPieceArmorStandBehaviour(ArmorInventory::SLOT_HEAD));
		$this->register(ItemFactory::air(), new NullItemArmorStandBehaviour());
	}

	public function register(Item $item, ArmorStandBehaviour $behaviour) : void{
		$this->behaviours[$item->getId()] = $behaviour;
	}

	public function registerFallback(ArmorStandBehaviour $behaviour) : void{
		$this->fallback = $behaviour;
	}

	public function get(Item $item) : ArmorStandBehaviour{
		return $this->behaviours[$item->getId()] ?? $this->fallback;
	}
}