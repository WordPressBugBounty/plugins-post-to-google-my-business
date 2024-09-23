<?php

namespace PGMB\RateLimiter;

class RateLimiter {

	/**
	 * @var string
	 */
	private $transient_name;

	public function __construct($identifier, $limit = 100, $timeframe = 'minute'){
		$this->transient_name = 'pgmb_ratelimiter_'.$identifier;
	}

	public function consume(){
		if($date = get_transient($this->transient_name)){

		}
	}
}