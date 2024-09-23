<?php

namespace PGMB\Metabox;

interface JSMetaboxInterface extends MetaboxInterface {
	/**
	 * Enqueue clientside assets
	 *
	 * @return void
	 */
	public function enqueue_scripts($hook);
}