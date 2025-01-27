<?php

namespace PGMB\BackgroundProcessing;

class AccountsSyncQueueItem {
	/**
	 * @var array
	 */
	private $account_ids;

	/**
	 * @param string[] $account_ids Google Sub IDs
	 */
	public function __construct(array $account_ids){
		$this->account_ids = $account_ids;
	}

	/**
	 * @return array|string[]
	 */
	public function get_account_ids(): array {
		return $this->account_ids;
	}
}