<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\entity;

use muqsit\pmarmorstand\entity\ticker\ArmorStandEntityTicker;
use muqsit\pmarmorstand\entity\ticker\WobbleArmorStandEntityTicker;
use muqsit\pmarmorstand\pose\ArmorStandPose;
use muqsit\pmarmorstand\pose\ArmorStandPoseRegistry;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;

class ArmorStandEntity extends Living{

	private const TAG_ARMOR_INVENTORY = "ArmorInventory";
	private const TAG_HELD_ITEM = "HeldItem";
	private const TAG_POSE = "Pose";

	public static function getNetworkTypeId() : string{
		return EntityIds::ARMOR_STAND;
	}

	protected $maxDeadTicks = 0;

	private ArmorStandPose $pose;
	protected Item $item_in_hand;

	/** @var ArmorStandEntityTicker[] */
	protected array $armor_stand_entity_tickers = [];

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.975, 0.5);
	}

	public function getName() : string{
		return "Armor Stand";
	}

	public function getDrops() : array{
		$drops = $this->getArmorInventory()->getContents();
		if(!$this->item_in_hand->isNull()){
			$drops[] = $this->item_in_hand;
		}
		$drops[] = ItemFactory::getInstance()->get(ItemIds::ARMOR_STAND);
		return $drops;
	}

	public function getItemInHand() : Item{
		return $this->item_in_hand;
	}

	public function setItemInHand(Item $item_in_hand) : void{
		$this->item_in_hand = $item_in_hand;
		$packet = MobEquipmentPacket::create($this->getId(), ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet($this->getItemInHand())), 0, ContainerIds::INVENTORY);
		foreach($this->getViewers() as $viewer){
			$viewer->getNetworkSession()->sendDataPacket($packet);
		}
	}

	public function getPose() : ArmorStandPose{
		return $this->pose;
	}

	public function setPose(ArmorStandPose $pose) : void{
		$this->pose = $pose;
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::ARMOR_STAND_POSE_INDEX, $pose->getNetworkId());
		$this->scheduleUpdate();
	}

	protected function sendSpawnPacket(Player $player) : void{
		parent::sendSpawnPacket($player);
		$player->getNetworkSession()->sendDataPacket(MobEquipmentPacket::create($this->getId(), ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet($this->getItemInHand())), 0, ContainerIds::INVENTORY));
	}

	protected function addAttributes() : void{
		parent::addAttributes();
		$this->setMaxHealth(6);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);

		$armor_inventory_tag = $nbt->getListTag(self::TAG_ARMOR_INVENTORY);
		if($armor_inventory_tag !== null){
			$armor_inventory = $this->getArmorInventory();
			/** @var CompoundTag $tag */
			foreach($armor_inventory_tag as $tag){
				$armor_inventory->setItem($tag->getByte("Slot"), Item::nbtDeserialize($tag));
			}
		}

		$item_in_hand_tag = $nbt->getCompoundTag(self::TAG_HELD_ITEM);
		$this->item_in_hand = $item_in_hand_tag !== null ? Item::nbtDeserialize($item_in_hand_tag) : ItemFactory::air();

		$this->setPose(($tag_pose = $nbt->getTag(self::TAG_POSE)) instanceof StringTag ?
			ArmorStandPoseRegistry::instance()->get($tag_pose->getValue()) :
			ArmorStandPoseRegistry::instance()->default());
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();

		$armor_pieces = [];
		foreach($this->getArmorInventory()->getContents() as $slot => $item){
			$armor_pieces[] = $item->nbtSerialize($slot);
		}
		$nbt->setTag(self::TAG_ARMOR_INVENTORY, new ListTag($armor_pieces, NBT::TAG_Compound));

		$nbt->setTag(self::TAG_HELD_ITEM, $this->item_in_hand->nbtSerialize());

		$nbt->setString(self::TAG_POSE, ArmorStandPoseRegistry::instance()->getIdentifier($this->pose));
		return $nbt;
	}

	public function applyDamageModifiers(EntityDamageEvent $source) : void{
	}

	public function attack(EntityDamageEvent $source) : void{
		parent::attack($source);
		if($source instanceof EntityDamageByChildEntityEvent && $source->getChild() instanceof Arrow){
			$this->kill();
		}
	}

	public function knockBack(float $x, float $z, float $base = 0.4) : void{
	}

	public function actuallyKnockBack(float $x, float $z, float $base = 0.4) : void{
		parent::knockBack($x, $z, $base);
	}

	protected function doHitAnimation() : void{
		if(
			$this->lastDamageCause instanceof EntityDamageByEntityEvent &&
			$this->lastDamageCause->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK &&
			$this->lastDamageCause->getDamager() instanceof Player
		){
			$this->addArmorStandEntityTicker("ticker:wobble", new WobbleArmorStandEntityTicker($this));
		}
	}

	protected function startDeathAnimation() : void{
	}

	public function addArmorStandEntityTicker(string $identifier, ArmorStandEntityTicker $ticker) : void{
		$this->armor_stand_entity_tickers[$identifier] = $ticker;
		$this->scheduleUpdate();
	}

	public function removeArmorStandEntityTicker(string $identifier) : void{
		unset($this->armor_stand_entity_tickers[$identifier]);
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		$result = parent::entityBaseTick($tickDiff);

		foreach($this->armor_stand_entity_tickers as $identifier => $ticker){
			if(!$ticker->tick($this)){
				$this->removeArmorStandEntityTicker($identifier);
			}
		}

		return $result || count($this->armor_stand_entity_tickers) > 0;
	}
}