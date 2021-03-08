<?php

declare(strict_types=1);

namespace muqsit\pmarmorstand\entity\ticker;

use muqsit\pmarmorstand\entity\ArmorStandEntity;

interface ArmorStandEntityTicker{

	public function tick(ArmorStandEntity $entity) : bool;
}