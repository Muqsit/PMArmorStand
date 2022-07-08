<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\pose;

final class SimpleArmorStandPose implements ArmorStandPose{

	public function __construct(
		private string $name,
		private int $network_id
	){}

	public function getName() : string{
		return $this->name;
	}

	public function getNetworkId() : int{
		return $this->network_id;
	}
}