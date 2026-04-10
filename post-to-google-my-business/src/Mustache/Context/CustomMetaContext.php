<?php

namespace PGMB\Mustache\Context;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomMetaContext extends AbstractContextProvider {

	public function get_key(): string {
		return 'meta';
	}

	public function build(): array {
		return [];
	}

	public function describe(): array {
		return [
			'label' => __('Custom meta fields', 'post-to-google-my-business'),
			'is_premium' => true,
			'children' => [],
		];
	}
}