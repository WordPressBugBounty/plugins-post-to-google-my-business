<?php

namespace PGMB\Metabox;

interface StorableDataMetaboxInterface extends MetaboxInterface {

	/**
	 * Handle submitted data
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function save_post($post_id);
}