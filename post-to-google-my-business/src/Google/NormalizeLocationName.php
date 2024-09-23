<?php

namespace PGMB\Google;

class NormalizeLocationName {

	private $account_id;
	private $location_id;

	public function __construct($account_id, $location_id){
		if(!is_numeric($account_id) || !is_numeric($location_id)){
			throw new \InvalidArgumentException('One of the passed IDs is not numeric');
		}
		$this->account_id = $account_id;
		$this->location_id = $location_id;
	}

	public static function from_with_account($name){
		if(!preg_match('/accounts\/(\d+)\/locations\/(\d+)/', $name, $matches)){
			return false;
		}
		return new static($matches[1], $matches[2]);
	}

	/**
	 * @param $name
	 * @param $account_id
	 *
	 * @return false|static
	 */
	public static function from_without_account($name, $account_id){
		if(!preg_match('/locations\/(\d+)/', $name, $matches)){
			return false;
		}
		return new static($account_id, $matches[1]);
	}


	/**
	 * New instance from a group name and a location name
	 *
	 * @param $group_name string Group name in the format "accounts/106802586615212834224"
	 * @param $location_name string Location name in the format "locations/10833174685256778669"
	 *
	 * @return NormalizeLocationName|false
	 */
	public static function from_group_and_location($group_name, $location_name){
		if(
			!preg_match('accounts\/(\d+)', $group_name, $group_matches) ||
			!preg_match('locations\/(\d+)', $location_name, $location_matches)
		){
			return false;
		}

		return new static($group_matches[1], $location_matches[1]);
	}

	/**
	 * @return string Location ID string with group/account ID included
	 */
	public function with_account_id(){
		return "accounts/{$this->account_id}/locations/{$this->location_id}";
	}

	/**
	 * @return string Location ID string without the account ID included
	 */
	public function without_account_id(){
		return "locations/{$this->location_id}";
	}

	public function account_id(){
		return $this->account_id;
	}

	public function location_id(){
		return $this->location_id();
	}
}