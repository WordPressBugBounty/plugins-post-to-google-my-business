<?php

namespace PGMB\ApiCache;

class Group {
	private $id;
	private $account_id;
	private $google_id;
	private $group_name;
	private $imported_at;

	//todo: abstract this
	public function __construct($data = []){
		foreach($data as $key => $value){
			if(property_exists($this, $key)){
				$this->{$key} = $value;
			}
		}
	}

	public function api_formatted(){
		$output = new \stdClass();
		$output->name = $this->google_id;
		$output->accountName = $this->group_name;
		return $output;
	}

	public function get_id(){
		return $this->id;
	}

}