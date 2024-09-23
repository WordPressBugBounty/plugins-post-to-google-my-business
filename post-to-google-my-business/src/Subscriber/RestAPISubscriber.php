<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;
use PGMB\REST\RouteInterface;

class RestAPISubscriber implements SubscriberInterface {

	const REST_API_VERSION = 'v1';

	const NAMESPACE = 'post-to-google-my-business';

	/**
	 * @var RouteInterface[]
	 */
	private $routes;

	/**
	 * @param RouteInterface[] $routes
	 */
	public function __construct( array $routes){
		$this->routes = $routes;
	}

	public static function get_subscribed_hooks(): array {
		return [
			'rest_api_init' => 'register_routes',
		];
	}

	public function register_routes(){
		foreach($this->routes as $route){
			register_rest_route( self::NAMESPACE.'/'.self::REST_API_VERSION, $route->get_endpoint_name(), [
				'methods'  => $route->get_methods(),
				'callback' => [$route, 'callback'],
				'permission_callback' => [$route, 'permission_callback'],
			] );
		}
	}
}