<?php

namespace PGMB\BackgroundProcessing;

class AccountSyncQueueItem {
	private $account_id;

	public function __construct(string $account_id) {
		$this->account_id = $account_id;
	}

	public function get_account_id(): string {
		return $this->account_id;
	}
}