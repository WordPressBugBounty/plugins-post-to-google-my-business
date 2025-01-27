<?php

namespace PGMB\ApiCache;

use stdClass;

class Location {
	private $id;
	private $group_id;
	private $google_id;
	private $store_code;
	private $title;
	private $language_code;
	private $website_uri;
	private $regular_hours;
	private $special_hours;
	private $labels;
	private $metadata;
	private $service_area;
	private $storefront_address;
	private $imported_at;

	public function __construct($data = []){
		foreach($data as $key => $value){
			if(property_exists($this, $key)){
				$this->{$key} = $value;
			}
		}
	}

	public function api_formatted(){
		$output = new stdClass();
		$output->name = $this->google_id;
		$output->storeCode = $this->store_code;
		$output->title = $this->title;
		$output->languageCode = $this->language_code;
		$output->websiteUri = $this->website_uri;
		$output->regularHours = json_decode($this->regular_hours);
		$output->specialHours = json_decode($this->special_hours);
		$output->labels = $this->labels;
		$output->serviceArea = json_decode($this->service_area);
		$output->metadata = json_decode($this->metadata);
		$output->storefrontAddress = json_decode($this->storefront_address);

		return $output;
	}

	public function get_title(){
		return $this->title;
	}

	public function get_storeCode(){
		return $this->store_code;
	}

	public function get_languageCode(){
		return $this->language_code;
	}
}