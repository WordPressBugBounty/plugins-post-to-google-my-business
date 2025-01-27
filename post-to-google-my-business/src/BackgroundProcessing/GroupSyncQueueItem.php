<?php

namespace PGMB\BackgroundProcessing;

class GroupSyncQueueItem extends AccountSyncQueueItem {
	/**
	 * @var string
	 */
	private $pageToken;

	public function __construct( $account_id, $pageToken = '' ) {
		parent::__construct( $account_id );
		$this->pageToken = $pageToken;
	}

	public function getPageToken() {
		return $this->pageToken;
	}
}