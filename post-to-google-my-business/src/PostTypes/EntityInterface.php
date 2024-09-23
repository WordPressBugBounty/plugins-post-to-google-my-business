<?php


namespace PGMB\PostTypes;


use WP_Post;

interface EntityInterface {
	/**
	 * Return an array of post data compatible with the wp_insert_post() function
	 *
	 * @return array
	 */
	public function get_post_data();

	/**
	 * Load an instance of the entity from a WP_Post object
	 *
	 * @param WP_Post $post
	 *
	 * @return self
	 */
	public static function from_post(WP_Post $post);

	public function get_id();

}
