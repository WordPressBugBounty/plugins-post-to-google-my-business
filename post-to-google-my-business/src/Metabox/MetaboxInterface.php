<?php

namespace PGMB\Metabox;

use WP_Post;
use WP_Screen;

interface MetaboxInterface {

	/**
	 * @return string WordPress metabox ID
	 */
	public function get_id();

	/**
	 * @return string Localized metabox title
	 */
	public function get_title();

	/**
	 * Echo Metabox HTML
	 *
	 * @param WP_Post $post
	 *
	 * @return void
	 */
	public function render_meta_box(WP_Post $post);

	/**
	 * @return string|array|WP_Screen $screen
	 */
	public function get_screen();

	/**
	 * @return void
	 */
	public function admin_init();
}