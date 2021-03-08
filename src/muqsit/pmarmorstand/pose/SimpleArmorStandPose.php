<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\pose;

final class SimpleArmorStandPose implements ArmorStandPose{

	/** @var string */
	private $name;

	/** @var int */
	private $network_id;

	public function __construct(string $name, int $network_id){
		$this->name = $name;
		$this->network_id = $network_id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getNetworkId() : int{
		return $this->network_id;
	}
}