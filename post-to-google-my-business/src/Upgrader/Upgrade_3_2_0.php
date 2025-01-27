<?php

namespace PGMB\Upgrader;

use PGMB\BackgroundProcessing\AccountSyncQueueItem;
use PGMB\BackgroundProcessing\LocationSyncProcess;
use PGMB\GoogleUserManager;
use PGMB\Plugin;

class Upgrade_3_2_0 implements Upgrade {
	/**
	 * @var LocationSyncProcess
	 */
	private $sync_process;
	/**
	 * @var GoogleUserManager
	 */
	private $user_manager;

	public function __construct(GoogleUserManager $user_manager, LocationSyncProcess $sync_process){
		$this->sync_process = $sync_process;
		$this->user_manager = $user_manager;
	}

	public function run() {
		Plugin::register_database_tables();

		$accounts = $this->user_manager->get_accounts();

		if(empty($accounts)){
			return;
		}

		foreach($accounts as $id => $account){
			$this->sync_process->push_to_queue(new AccountSyncQueueItem($id))->save()->dispatch();
		}

	}
}