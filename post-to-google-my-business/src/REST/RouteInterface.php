<?php

namespace PGMB\REST;

use WP_Error;
use WP_REST_Response;

interface RouteInterface {
	public function get_endpoint_name() : string;

	public function get_methods() : string;

	/**
	 * @return WP_REST_Response|WP_Error
	 */
	public function callback();

	public function permission_callback() : bool;
}