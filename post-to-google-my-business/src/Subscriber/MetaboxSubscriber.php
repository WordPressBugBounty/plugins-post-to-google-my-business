<?php

namespace PGMB\Subscriber;

use PGMB\Admin\AjaxCallbackInterface;
use PGMB\EventManagement\EventManager;
use PGMB\EventManagement\EventManagerAwareSubscriberInterface;
use PGMB\Metabox\JSMetaboxInterface;
use PGMB\Metabox\MetaboxInterface;
use PGMB\Metabox\StorableDataMetaboxInterface;

class MetaboxSubscriber implements EventManagerAwareSubscriberInterface {

	private $metaboxes;

	/**
	 * @var EventManager
	 */
	private $event_manager;

	/**
	 * @param MetaboxInterface[] $metaboxes
	 */
	public function __construct( array $metaboxes){
		$this->metaboxes = $metaboxes;
	}

	public static function get_subscribed_hooks() {
//		$post_type = PostTypeAutoPostTemplate::POST_TYPE;
		return [
			'add_meta_boxes'        => 'register_metaboxes',
			'admin_init'            => 'initialize',
			'save_post'             => 'save_post',
			'admin_enqueue_scripts' => 'enqueue_scripts',
			'init'                  => 'register_ajax_callbacks',
		];
	}

	public function enqueue_scripts($hook){
		if(!in_array($hook, [ 'post.php', 'post-new.php' ] )){
			return;
		}

		$screen = get_current_screen();
		if(!is_object($screen)){
			return;
		}

		foreach($this->metaboxes as $metabox){
			if(!$metabox instanceof JSMetaboxInterface || !in_array($screen->post_type, $metabox->get_screen()) ){ //!== PostTypeAutoPostTemplate::POST_TYPE
				continue;
			}
			$metabox->enqueue_scripts($hook);
		}
	}

	public function register_metaboxes(){
		foreach($this->metaboxes as $metabox){
			add_meta_box(
				$metabox->get_id(),
				$metabox->get_title(),
				[$metabox, 'render_meta_box'],
				$metabox->get_screen()
			);
		}

	}

	public function initialize(){
		foreach($this->metaboxes as $metabox) {
			$metabox->admin_init();
		}
	}

	public function register_ajax_callbacks(){
		foreach($this->metaboxes as $metabox){
			if(!$metabox instanceof AjaxCallbackInterface){ continue; }
			foreach($metabox->ajax_callbacks() as $hook => $callback){
				$this->event_manager->add_callback("wp_ajax_mbp_{$hook}", $callback);
			}
		}
	}

	public function save_post($post_id){
		foreach($this->metaboxes as $metabox) {
			if(!$metabox instanceof StorableDataMetaboxInterface) { continue; }
			$metabox->save_post($post_id);
		}

	}

	public function set_event_manager( EventManager $event_manager ) {
		$this->event_manager = $event_manager;
	}
}
