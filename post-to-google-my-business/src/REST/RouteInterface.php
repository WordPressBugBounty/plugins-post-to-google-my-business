<?php

namespace PGMB\REST;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

interface RouteInterface {
	public function get_arguments(): array;

	public function respond(WP_REST_Request $request);

	public function validate(WP_REST_Request $request): bool;

	public function get_methods() : array;

	public function get_path() : string;
}