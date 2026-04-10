<?php

namespace PGMB\Admin;

abstract class AbstractPage {

	protected $template_path;

	public $plugin_url;

	public function __construct($template_path, $plugin_url){
		$this->template_path = $template_path;
		$this->plugin_url = $plugin_url;
	}

	public function get_parent_slug(){
		return 'post_to_google_my_business';
	}

	public function get_capability(){
		return apply_filters('pgmb_page_cap', 'manage_options', $this);
	}

	abstract public function get_menu_slug();

	abstract public function get_page_title();

	abstract public function get_menu_title();

	abstract protected function render_content();

	public function render_page(){
		if(!current_user_can($this->get_capability())){
			wp_die(__('You do not have sufficient permissions to access this page.', 'post-to-google-my-business'));
		}
		$this->render_content();
	}

	/**
	 * @return int Page position in submenu
	 */
	abstract public function get_position();
}
