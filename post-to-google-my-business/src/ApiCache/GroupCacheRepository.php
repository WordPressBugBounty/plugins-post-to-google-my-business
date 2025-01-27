<?php

namespace PGMB\ApiCache;

use wpdb;

class GroupCacheRepository {
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var string
	 */
	private $table;

	public function __construct(Wpdb $wpdb) {
		$this->wpdb = $wpdb;
		$this->table = $wpdb->prefix.'pgmb_group_cache';
	}

	/**
	 * @param $account_id - Google "Sub" ID
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return Group[]
	 */
	public function get_groups_by_account_id($account_id, int $limit = 20, int $offset = 0) {
		$results = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM $this->table WHERE account_id = %s AND in_latest_import=1 LIMIT %d OFFSET %d", $account_id, $limit, $offset), ARRAY_A);
		return array_map( function ($row) {
			return new Group( $row );
		}, $results);
	}
}