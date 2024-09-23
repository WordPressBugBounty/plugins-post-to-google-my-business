<?php

namespace PGMB\Subscriber;

use PGMB\Admin\AbstractPage;
use PGMB\Admin\AjaxCallbackInterface;
use PGMB\Admin\ConfigurablePageInterface;
use PGMB\Admin\DashboardPage;
use PGMB\Admin\EnqueuesScriptsInterface;
use PGMB\EventManagement\EventManager;
use PGMB\EventManagement\EventManagerAwareSubscriberInterface;
use PGMB\Notifications\NotificationManager;

class AdminPageSubscriber implements EventManagerAwareSubscriberInterface {

	/**
	 * @var DashboardPage
	 */
	private $main_page;
	private $dashicon;
	/**
	 * @var EventManager
	 */
	private $event_manager;
	private $admin_pages;
	/**
	 * @var NotificationManager
	 */
	private $notification_manager;

	private $hooked_pages;

	public function __construct(DashboardPage $main_page, $admin_pages, $dashicon, NotificationManager $notification_manager) {
		$this->main_page = $main_page;
		$this->dashicon  = $dashicon;
		$this->admin_pages = $admin_pages;
		$this->notification_manager = $notification_manager;
	}

	public static function get_subscribed_hooks() {
		return [
			'admin_menu' => 'add_admin_pages',
			'init'       => 'register_ajax_callbacks',
			'admin_init' => 'configure_pages'
		];
	}

	public function get_notification_count_html(){
		if(mbp_fs()->is_in_trial_promotion()){
			return '';
		}
		$count = $this->notification_manager->notification_count('dashboard-notifications');
		if($count >= 1){
			return '<span class="update-plugins"><span class="update-count">'.$count.'</span></span>';
		}
		return '';
	}

	public function add_admin_pages(){
		add_menu_page(
			__('Post to Google My Business', 'post-to-google-my-business'),
			sprintf(__('Post to GMB %s', 'post-to-google-my-business'), $this->get_notification_count_html()),
			$this->main_page->get_capability(),
			$this->main_page->get_menu_slug(),
			[$this->main_page, 'render_page' ],
			$this->dashicon
		);
		foreach($this->admin_pages as $admin_page){
			if(!$admin_page instanceof AbstractPage){ continue; }

			$page = add_submenu_page(
				$admin_page->get_parent_slug(),
				$admin_page->get_page_title(),
				$admin_page->get_menu_title(),
				$admin_page->get_capability(),
				$admin_page->get_menu_slug(),
				[$admin_page, 'render_page' ],
				$admin_page->get_position()
			);
			if($page && $admin_page instanceof EnqueuesScriptsInterface){
				$hook = "load-{$page}";
				$this->event_manager->add_callback($hook, [$this, 'enqueue_page_scripts_conditional']);
				$this->hooked_pages[$hook] = [$admin_page, 'enqueue_scripts'];
			}
		}

	}

	public function enqueue_page_scripts_conditional(){
		$hook = $this->event_manager->get_current_hook();
		$this->event_manager->add_callback('admin_enqueue_scripts', $this->hooked_pages[$hook]);
	}

	public function register_ajax_callbacks(){
		foreach($this->admin_pages as $admin_page){
			if(!$admin_page instanceof AjaxCallbackInterface){ continue; }

			foreach($admin_page->ajax_callbacks() as $hook => $callback){
				$this->event_manager->add_callback("wp_ajax_{$hook}", $callback);
			}
		}
	}

	public function configure_pages(){
		foreach($this->admin_pages as $admin_page){
			if($admin_page instanceof ConfigurablePageInterface){
				$admin_page->configure();
			}
		}
	}

	public function set_event_manager( EventManager $event_manager ) {
		$this->event_manager = $event_manager;
	}
}
