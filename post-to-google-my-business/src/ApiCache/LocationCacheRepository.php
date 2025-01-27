<?php

namespace PGMB\ApiCache;

use Exception;
use wpdb;

class LocationCacheRepository {
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var string
	 */
	private $table;
	/**
	 * @var string
	 */
	private $groups_table;

	public function __construct(wpdb $wpdb) {
		$this->wpdb = $wpdb;
		$this->table = $wpdb->prefix.'pgmb_location_cache';
		
		$this->groups_table = $wpdb->prefix.'pgmb_group_cache';
	}

	/**
	 * @param int $group_id
	 *
	 * @return Location[]
	 */
	public function get_locations_by_group_id(int $group_id): array {
		$results = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE group_id = %d", $group_id), ARRAY_A);
		return array_map( function ($row) {
			return new Location( $row );
		}, $results);
	}

	public function get_locations_by_group_google_id($google_id, int $limit = 100, int $offset = 0): array {
		/*
		 *
		 *         SELECT l.*
        FROM $locationsTable l
        INNER JOIN $groupsTable g ON l.group_id = g.id
        WHERE g.google_id = %s

		 */
		$results = $this->wpdb->get_results($this->wpdb->prepare("SELECT l.* FROM {$this->table} l INNER JOIN {$this->groups_table} g ON l.group_id = g.id WHERE g.google_id = %s AND l.in_latest_import=1 LIMIT %d OFFSET %d", $google_id, $limit, $offset), ARRAY_A);
		return array_map( function ($row) {
			return new Location( $row );
		}, $results);
	}

	/**
	 * Queries Google locations by their Google ID, returns an array of Location indexed by the Google ID
	 *
	 * @throws Exception
	 * @return Location[]
	 */
	public function get_locations_by_google_ids(array $google_ids): array {
		if(empty($google_ids)){
			return [];
		}

		$placeholders = implode(',', array_fill(0, count($google_ids), '%s'));

		$query = "
        SELECT *
        FROM {$this->table}
        WHERE google_id IN ($placeholders)
    ";

		$prepared_query = $this->wpdb->prepare($query, ...$google_ids);
		$results = $this->wpdb->get_results($prepared_query, ARRAY_A);

		if($results === false){
			throw new Exception(sprintf(__("Failed to retrieve location data for %s: %s", 'post-to-google-my-business'), $placeholders, $this->wpdb->last_error));
		}

		$locations = [];
		foreach($results as $row){
			$location = new Location($row);
			$locations[$row['google_id']] = $location;
		}
		return $locations;
	}

	/**
	 * @param $google_id
	 *
	 * @return Location
	 * @throws Exception
	 */
	public function get_location_by_google_id($google_id): Location {
		$result = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE google_id = %s", $google_id), ARRAY_A);
		if($result === null){
			throw new Exception(__("Failed to retrieve location data for %s: %s", 'post-to-google-my-business'), $google_id, $this->wpdb->last_error);
		}
		return new Location( $result );
	}
}