<?php


namespace PGMB\EventManagement;


abstract class AbstractEventManagerAwareSubscriber implements EventManagerAwareSubscriberInterface {

	/**
	 * The WordPress event manager.
	 *
	 * @var EventManager
	 */
	protected $event_manager;

	/**
	 * Set the WordPress event manager for the subscriber.
	 *
	 * @param EventManager $event_manager
	 */
	public function set_event_manager( EventManager $event_manager ) {
		$this->event_manager = $event_manager;
	}
}
