<?php

namespace PGMB\Admin;

class AutoPostTemplateUpsell extends AbstractPage {

	public function get_page_title() {
		return __('Auto-post templates', 'post-to-google-my-business');
	}

	public function get_menu_title() {
		return __('Auto-post templates', 'post-to-google-my-business');
	}

	public function render_page() {
		include($this->template_path.'autopost_template.php');
	}

	public function get_position() {
		return 2;
	}

	public function get_menu_slug() {
		return 'pgmb_template_upsell';
	}
}
