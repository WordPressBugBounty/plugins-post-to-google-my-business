<?php


namespace PGMB\PostTypes;


class SubPostDefinition implements PostTypeDefinition {
	const POST_TYPE = 'mbp-google-subposts';

	public static function post_type_args() {
		return
		[
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'can_export' => true
		];
	}
}
