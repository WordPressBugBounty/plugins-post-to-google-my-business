<?php

namespace PGMB\BackgroundProcessing;


class LocationSyncQueueItem extends GroupSyncQueueItem {
	private $parent;

	public function __construct( $account_id, $parent, $pageToken = '' ) {
		parent::__construct( $account_id, $pageToken );
		$this->parent = $parent;
	}

	/**
	 * @return mixed
	 */
	public function get_parent() {
		return $this->parent;
	}
}