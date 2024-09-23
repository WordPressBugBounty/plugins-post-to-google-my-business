<?php


namespace PGMB\Subscriber;


use PGMB\EventManagement\SubscriberInterface;
use PGMB\PostTypes\GooglePostEntity;
use PGMB\PostTypes\SubPostDefinition;

class PostTypesSubscriber implements SubscriberInterface {

	public static function get_subscribed_hooks() {
		return [
			'init' => 'register_post_types'
		];
	}

	public function register_post_types(){
		register_post_type(GooglePostEntity::POST_TYPE);

		register_post_type(SubPostDefinition::POST_TYPE, SubPostDefinition::post_type_args());

	}
}
