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
		return 'manage_options';
	}

	abstract public function get_menu_slug();

	abstract public function get_page_title();

	abstract public function get_menu_title();

	abstract public function render_page();

	/**
	 * @return int Page position in submenu
	 */
	abstract public function get_position();
}
