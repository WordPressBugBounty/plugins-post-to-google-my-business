<?php

namespace PGMB\Admin;

class PostCampaignsUpsell extends AbstractPage {

	public function get_page_title() {
		return __('Post Campaigns', 'post-to-google-my-business');
	}

	public function get_menu_title() {
		return __('Post Campaigns', 'post-to-google-my-business');
	}

	public function render_page() {
		include($this->template_path.'postcampaigns.php');
	}

	public function get_position() {
		return 3;
	}

	public function get_menu_slug() {
		return 'pgmb_postcampaign_upsell';
	}
}
