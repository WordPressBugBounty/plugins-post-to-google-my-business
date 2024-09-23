<?php


namespace PGMB\Notifications;


class NotificationManager {

	private $prefix;
	private $notifications;
//	private $ignored_notifications;

	public function __construct($prefix) {
		$this->prefix = $prefix;
	}

	protected function load_notifications(){
		$this->notifications = get_option("{$this->prefix}_notifications");
		if(empty($this->notifications)){
			$this->notifications = [];
		}
	}

	public function init(){
		if(!$this->notifications){ $this->load_notifications(); }
	}

	protected function save_notifications(){
		update_option("{$this->prefix}_notifications", $this->notifications);
	}

	public function get_notifications($section, $limit = 5){
		if(!$this->notifications){ $this->load_notifications(); }
		if(!isset($this->notifications[$section]) || !is_array($this->notifications[$section])){ return []; }

		return array_slice(array_reverse($this->notifications[$section]),0, $limit);
	}

	public function add_notification(Notification $notification){
		if(!$this->notifications){ $this->load_notifications(); }

		$this->notifications[$notification->get_section()][$notification->get_identifier()] = $notification->get_data();

		$this->save_notifications();
	}

	public function notification_count($section){
		if(!$this->notifications){ $this->load_notifications(); }
		return count($this->get_notifications($section));
	}

	public function delete_notification($section, $identifier, $ignore = false){
		if(!$this->notifications){ $this->load_notifications(); }
		unset($this->notifications[$section][$identifier]);
		$this->save_notifications();
	}

}
