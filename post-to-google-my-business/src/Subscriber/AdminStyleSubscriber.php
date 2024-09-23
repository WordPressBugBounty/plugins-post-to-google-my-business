<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;

class AdminStyleSubscriber implements SubscriberInterface {

	private $plugin_url;
	private $plugin_version;

	public function __construct($plugin_url, $plugin_version){

		$this->plugin_url = $plugin_url;
		$this->plugin_version = $plugin_version;
	}
	public static function get_subscribed_hooks() {
		return [
			'admin_enqueue_scripts' => 'register_admin_style'
		];
	}

	public function register_admin_style(){
		wp_enqueue_style('mbp_admin_styles', $this->plugin_url.'/css/style.css', [ 'dashicons' ], $this->plugin_version);
	}
}