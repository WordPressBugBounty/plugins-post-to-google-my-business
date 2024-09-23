<?php


namespace PGMB\PostTypes;

use DateTime;
use Exception;
use InvalidArgumentException;
use PGMB\ParseFormFields;
use WP_Post;

class SubPost implements EntityInterface {
	const POST_TYPE	= 'mbp-google-subposts';

	private $is_autopost = false;

	private $parsed_form_fields;

	private $form_fields;

	private $has_error;

	protected $parent_id = 0;
	protected $editing_id = 0;

	private $post_publish_date_timestamp;


	public function __construct($ID, $parent_id) {
//		$this->form_fields = FormFields::default_post_fields();
		$this->editing_id = $ID;
		$this->parent_id = $parent_id;
	}

	private $draft = false;


	public function set_parent($parent_id){
		if(!is_numeric($parent_id)){ throw new InvalidArgumentException('Parent post ID is not numeric'); }
		$this->parent_id = intval($parent_id);
	}

	public function get_parent(){
		return $this->parent_id;
	}

	public function set_editing($post_id){
		if(!is_numeric($post_id)){ throw new InvalidArgumentException('Editing Post ID is not numeric'); }
		$this->editing_id = intval($post_id);
	}



	public function get_post_data() {
		return [
			'ID' => $this->editing_id,
			'post_parent' => $this->parent_id,
			'post_type' => self::POST_TYPE,
			'meta_input' => [
				'mbp_form_fields'		=> $this->form_fields,
				'_mbp_is_autopost'      => $this->is_autopost,
				'_mbp_post_publish_date'    => $this->post_publish_date_timestamp,
				'mbp_last_error'        => $this->has_error,
			],
			'post_status' => $this->draft ? 'draft' : 'publish'
		];
	}


	public function set_form_fields($fields){
		if(!is_array($fields)){ throw new InvalidArgumentException("Form fields expects an array"); }
		$this->form_fields = $fields;
	}

	public function get_form_fields(){
		return $this->form_fields;
	}

	public function get_id(){
		return $this->editing_id;
	}

	public function set_draft($draft = true){
		if($draft){
			$this->draft = true;
			return;
		}
		$this->draft = false;
	}

	public function set_autopost($autopost = true){
		$this->is_autopost = (bool)$autopost;
	}

	public function set_has_errors($error){
		$this->has_error = $error;
	}

	public function has_error(){
		return $this->has_error;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return SubPost|static
	 */
	public static function from_post( WP_Post $post ) {
		$instance = new static($post->ID, $post->post_parent);
		$instance->set_form_fields($post->mbp_form_fields);
		$instance->set_autopost($post->_mbp_is_autopost);
		$instance->set_post_publish_date_timestamp($post->_mbp_post_publish_date);
		$instance->set_has_errors($post->mbp_last_error);
		$instance->set_draft( $post->post_status === 'draft' );
		return $instance;
	}

	public function get_creation_timestamp(){
		return get_post_time('U', true, $this->editing_id);
	}

	public function get_post_publish_date_timestamp(){
		//Backwards compatibility
		if($this->form_fields && !$this->post_publish_date_timestamp){
			try{
				$parsed_form_fields = $this->parsed_form_fields();
				$publish_DateTime = $parsed_form_fields->getPublishDateTime();
				if($publish_DateTime){
					$this->post_publish_date_timestamp = $publish_DateTime->getTimestamp();
				}
			}catch (Exception $exception){
				$has_error = true;
			}
			if(!isset($publish_DateTime) || !$publish_DateTime instanceof DateTime){
				$this->post_publish_date_timestamp = $this->get_creation_timestamp();
			}
		}

		return $this->post_publish_date_timestamp;
	}

	public function set_post_publish_date_timestamp($unix_timestamp){
		$this->post_publish_date_timestamp = (int)$unix_timestamp;
	}

	public function is_draft(){
		return $this->draft;
	}

	/**
	 * @return ParseFormFields
	 */
	public function parsed_form_fields(){
		if(!$this->parsed_form_fields){
			$this->parsed_form_fields = new ParseFormFields($this->form_fields);
		}
		return $this->parsed_form_fields;
	}

	public static function create($parent_id){
		return new static(0, $parent_id);
	}
}
