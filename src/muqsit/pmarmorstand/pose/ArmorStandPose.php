<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\pose;

interface ArmorStandPose{

	public function getName() : string;

	public function getNetworkId() : int;
}