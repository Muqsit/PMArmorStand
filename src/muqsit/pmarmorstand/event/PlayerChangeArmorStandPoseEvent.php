<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\event;

use muqsit\pmarmorstand\entity\ArmorStandEntity;
use muqsit\pmarmorstand\pose\ArmorStandPose;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;
use pocketmine\player\Player;

/**
 * @extends EntityEvent<ArmorStandEntity>
 */
final class PlayerChangeArmorStandPoseEvent extends EntityEvent implements Cancellable{
	use CancellableTrait;

	protected ArmorStandPose $old_pose;
	protected ArmorStandPose $new_pose;
	protected Player $causer;

	public function __construct(ArmorStandEntity $entity, ArmorStandPose $old_pose, ArmorStandPose $new_pose, Player $causer){
		$this->entity = $entity;
		$this->old_pose = $old_pose;
		$this->new_pose = $new_pose;
		$this->causer = $causer;
	}

	public function getOldPose() : ArmorStandPose{
		return $this->old_pose;
	}

	public function getNewPose() : ArmorStandPose{
		return $this->new_pose;
	}

	public function setNewPose(ArmorStandPose $new_pose) : void{
		$this->new_pose = $new_pose;
	}

	public function getCauser() : Player{
		return $this->causer;
	}
}