<?php

namespace PGMB\PostTypes;

use Exception;

class AutoPostFactory {
	private $default_autopost_template;

	public function __construct($default_autopost_template){

		$this->default_autopost_template = $default_autopost_template;
	}
	public function create_autopost($post_id){
		$subpost = SubPost::create($post_id);

		$savedAutopostTemplate = get_post_meta($post_id, '_mbp_autopost_template', true);
		if($savedAutopostTemplate) {
			$subpost->set_form_fields( $savedAutopostTemplate );
		}elseif(mbp_fs()->is_plan_or_trial__premium_only('pro') && $template_id = get_post_meta($post_id, '_pgmb_ap_template_id', true)) {
			$template = get_post_meta( $template_id, '_pgmb_autopost_template', true );
			$subpost->set_form_fields( $template );
		}else{
//			$defaultAutoPostTemplate = $this->settings_api->get_option('autopost_template', 'mbp_quick_post_settings', \PGMB\FormFields::default_autopost_fields());
			if(empty($this->default_autopost_template)) { $this->default_autopost_template = \PGMB\FormFields::default_autopost_fields(); }
			$subpost->set_form_fields($this->default_autopost_template);
		}

		$subpost->set_autopost();

		if(!$subpost = apply_filters('mbp_autopost_before_insert_subpost', $subpost)){
			return false; //Filter to alter or cancel the autopost
		}

		try{
			$child_post_id = wp_insert_post($subpost->get_post_data(), true);

		}catch(Exception $e){
//			error_log($e->getMessage());
		}

		update_post_meta($post_id, 'mbp_autopost_created', true);
		update_post_meta($post_id, '_mbp_gutenberg_autopost', false);
		return $subpost;
	}
}