<?php


namespace PGMB\PostTypes;


use WP_Post;

class GooglePostEntity implements EntityInterface {

	const POST_TYPE = 'mbp_post_entity';

	/**
	 * To which this post this GMB post entity belongs
	 *
	 * @var int
	 */
	private $post_parent = 0;

	/**
	 * Google account ID (sub)
	 *
	 * @var int
	 */
	private $user_key;

	/**
	 * The Google ID of the location
	 *
	 * @var string
	 */
	private $location_id;

	/**
	 * Post state on Google
	 *
	 * @var mixed|string
	 */
	private $state;

	/**
	 * Url of the post in the Google SERP
	 *
	 * @var mixed|string
	 */
	private $searchUrl;

	/**
	 * ID ("name") of the post on Google
	 *
	 * @var mixed|string
	 */
	private $name;

	/**
	 * Error message that may have occurred when creating the post
	 *
	 * @var mixed|string
	 */
	private $last_error_message;


//	public static function create($user_key, $user_email, $group_id){
//		$instance = new self();
//
//		return $instance;
//	}

	/**
	 * WordPress post ID
	 *
	 * @var int
	 */
	private $ID;

	private function __construct( $ID, $user_key, $location_id, $name = '', $state = '', $searchUrl = '', $last_error_message = '' ){
		$this->user_key = $user_key;
		$this->location_id = $location_id;
		$this->ID = $ID;
		$this->state = $state;
		$this->searchUrl = $searchUrl;
		$this->name = $name;
		$this->last_error_message = $last_error_message;
	}

	/**
	 * Post data for wp_insert_post()
	 *
	 * @return array
	 */
	public function get_post_data(){
		return [
			'ID'                => $this->ID,
			'comment_status'    => 'closed',
			'post_type'         => self::POST_TYPE,
			'post_status'       => 'publish',
			'post_parent'       => $this->post_parent,
			'meta_input'        => [
				'_pgmb_user_key'         => $this->user_key,
				'_pgmb_location_id'      => $this->location_id,
				'_pgmb_post_name'        => $this->name,
				'_pgmb_post_state'       => $this->state,
				'_pgmb_post_searchUrl'      => $this->searchUrl,
				'_pgmb_last_error_message'  => $this->last_error_message
			]
		];
	}

	/**
	 * Create a PostEntity object from a WP_Post instance
	 *
	 *
	 * @param WP_Post $post
	 *
	 * @return GooglePostEntity|void
	 */
	public static function from_post(WP_Post $post){
		if(!isset($post->_pgmb_user_key, $post->_pgmb_location_id)){
			return;
		}

		$instance = new static( $post->ID, $post->_pgmb_user_key, $post->_pgmb_location_id, $post->_pgmb_post_name, $post->_pgmb_post_state, $post->_pgmb_post_searchUrl, $post->_pgmb_last_error_message);
		$instance->set_post_parent($post->post_parent);

		return $instance;
	}

	/**
	 * Create a PostEntity instance from an API result
	 *
	 * @param $user_key
	 * @param $location_id
	 *
	 * @return GooglePostEntity
	 */
	public static function from_api($user_key, $location_id){
		return new self(0, $user_key, $location_id);
	}

	/**
	 * Set the parent post ID
	 *
	 * @param $post_id
	 *
	 * @return GooglePostEntity
	 */
	public function set_post_parent($post_id){
		if(!is_numeric($post_id)){
			throw new \InvalidArgumentException('Parent post ID should be numeric');
		}
		$this->post_parent = $post_id;
		return $this;
	}

	/**
	 * Add post data from a successful API request
	 *
	 * @param $name
	 * @param $state
	 * @param $searchUrl
	 */
	public function set_post_success($name, $state, $searchUrl){
		$this->name = $name;
		$this->state = $state;
		$this->searchUrl = $searchUrl;
		$this->last_error_message = null;
	}

	public function get_id() {
		return $this->ID;
	}

	/**
	 * Sets an error message in case of a failed API request
	 *
	 * @param $error_message
	 */
	public function set_post_failure($error_message){
		$this->last_error_message = (string)$error_message;
	}

	public function set_post_state($state){
		$this->state = $state;
		return $this;
	}

	public function get_post_name(){
		return $this->name;
	}

	public function get_user_key(){
		return $this->user_key;
	}

	public function get_post_state(){
		return $this->state;
	}

	public function get_location_id(){
		return $this->location_id;
	}

	public function get_searchUrl(){
		return $this->searchUrl;
	}

	public function get_post_error(){
		return $this->last_error_message;
	}
}
