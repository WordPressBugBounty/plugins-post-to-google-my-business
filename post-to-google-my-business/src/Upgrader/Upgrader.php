<?php


namespace PGMB\Upgrader;

use InvalidArgumentException;
use PGMB\EventManagement\SubscriberInterface;


class Upgrader implements SubscriberInterface {

	private $database_version;
	private $plugin_version;


	protected $plugin_prefix;


	protected $available_upgrades;
	/**
	 * @var UpgradeBackgroundProcess
	 */
	private $upgrade_process;

	public static function get_subscribed_hooks() {
		return [
			'init'  => [ 'run_for_required_updates', 20]
		];
	}

	/**
	 * @param $version PHP-standardized version number
	 *
	 * @return bool Supplied version number is valid
	 */
	public function validate_version_number($version){
		if(version_compare( $version, '0.0.1', '>=' )){
			return true;
		}
		return false;
	}

	public function get_database_version(){
		return get_option($this->plugin_prefix.'_version');
	}

	public function __construct(UpgradeBackgroundProcess $upgrade_process, $plugin_version, $plugin_prefix, $available_upgrades) {

		$this->plugin_prefix = $plugin_prefix;

		$this->plugin_version   = $plugin_version;
		$this->database_version = $this->get_database_version();
		//If there is no version saved into the database, there is nothing to upgrade (fresh install)
		if(!$this->database_version){
			update_option( $this->plugin_prefix . '_version', $this->plugin_version);
			return;
		}


		if(!$this->validate_version_number($this->database_version) || !$this->validate_version_number($this->plugin_version)){
			throw new InvalidArgumentException("Invalid version number(s) supplied to Upgrader constructor");
		}

		$this->available_upgrades = $available_upgrades;
		$this->upgrade_process = $upgrade_process;
	}

	public function is_upgrade_running(){
		return get_option( $this->plugin_prefix . '_upgrade_running');
	}

	protected function set_upgrade_running(){
		update_option( $this->plugin_prefix . '_upgrade_running', time());
	}

	public function stop_upgrade_running(){
		delete_option( $this->plugin_prefix . '_upgrade_running');
	}

	public function run_for_required_updates(){
		if(!$this->database_version || version_compare($this->plugin_version, $this->database_version, '==')){ return; } //If the latest version is already installed

//
		if($this->is_upgrade_running()){ return; }

		$this->set_upgrade_running();

		$distributed_upgrades = false;

		foreach($this->get_required_upgrades() as $upgrade){
			$upgrade_instance = $upgrade();
			if(!$upgrade_instance instanceof Upgrade){ continue; }

			if($upgrade_instance instanceof DistributedUpgrade){
				$distributed_upgrades = true;
				$upgrade_instance->set_background_process($this->upgrade_process);
			}
			$upgrade_instance->run();

		}
		update_option( $this->plugin_prefix . '_version', $this->plugin_version);

		$this->stop_upgrade_running();
	}

	protected function get_required_upgrades(){
		$required_upgrades = [];
		foreach($this->available_upgrades as $upgrade_version => $upgrade){
			if(version_compare($this->database_version, $upgrade_version, '<')){
				$required_upgrades[$upgrade_version] = $upgrade;
			}
		}
		return $required_upgrades;
	}

}
