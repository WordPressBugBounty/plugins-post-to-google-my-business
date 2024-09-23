<?php

namespace PGMB\Upgrader;

use PGMB_Vendor_WP_Background_Process;

class UpgradeBackgroundProcess extends PGMB_Vendor_WP_Background_Process{

	protected $action;

	/**
	 * @var false
	 */

	public function push_to_queue( $data, $callback = false) {
		$item = [
			'callback'  => $callback,
			'data'      => $data
		];
		return parent::push_to_queue( $item );
	}


	public function __construct($plugin_prefix) {
		$this->action        = $plugin_prefix . '_upgrade_process';
		parent::__construct();
	}

	/**
	 * @inheritDoc
	 */
	protected function task( $item ) {
//		if(!$upgrade = $this->available_upgrades[$item['version']]){ return false; }
//		$upgrade_instance = $upgrade();
//		if($upgrade_instance instanceof DistributedUpgrade) {
//			return $upgrade_instance->task( $item );
//		}
		if(is_callable($item['callback'])){
			return call_user_func($item['callback'], $item['data']);
		}

		return false;
	}

	protected function complete() {
		parent::complete();

	}
}
