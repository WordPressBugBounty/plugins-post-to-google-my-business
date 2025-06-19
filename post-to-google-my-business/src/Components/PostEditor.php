<?php


namespace PGMB\Components;

use DateTime;
use PGMB\FormFields;
use PGMB\Util\DateTimeCompat;

class PostEditor {

	private $ajax;

	public $fields;

	public $field_name;
	private $template_dir;


	public function __construct( $template_dir, $isAjax = false, $values = [], $field_name = 'mbp_form_fields' ) {
		$this->ajax         = $isAjax;
		$this->field_name   = $field_name;
		$this->set_values($values);
		$this->template_dir = $template_dir;
	}

	public function set_field_name($field_name){
		$this->field_name = $field_name;
	}

	public function set_ajax_enabled($ajax_enabled){
		$this->ajax = $ajax_enabled;
	}

	public function set_values($values){
		$this->fields = array_merge(FormFields::default_post_fields(), $values);
	}

	public function generate(){
		ob_start();
		require_once($this->template_dir.'posteditor.php' );


		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}


	public function is_ajax_enabled(){
		return $this->ajax;
	}

	public function register_ajax_callbacks($prefix){

		add_action("wp_ajax_{$prefix}_check_date", [$this, 'ajax_validate_time' ]);

	}

	public function ajax_validate_time(){
		$timestring = sanitize_text_field($_POST['timestring']);

		//Check if the string contains a % indicating a variable
		if(strpos($timestring, '%') !== false){
			wp_send_json_success(__('Dynamic date, calculated/retrieved when GBP post is published', 'post-to-google-my-business'));
		}

		try{
			$datetime = new DateTime($timestring, DateTimeCompat::get_timezone());
		}catch(\Exception $e){
			wp_send_json_error();
		}

		/* translators: date time, Timezone: timezone */
		wp_send_json_success(sprintf(__('%1$s %2$s, Timezone: %3$s', 'post-to-google-my-business'), DateTimeCompat::format_date($datetime), DateTimeCompat::format_time($datetime), DateTimeCompat::get_timezone()->getName()));
	}
}
