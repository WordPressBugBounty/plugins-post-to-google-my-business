<?php

namespace PGMB\Upgrader;

interface DistributedUpgrade extends Upgrade {
	public function set_background_process(UpgradeBackgroundProcess $upgrader);
}
