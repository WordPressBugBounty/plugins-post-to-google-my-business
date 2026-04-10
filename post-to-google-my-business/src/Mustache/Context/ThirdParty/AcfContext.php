<?php

namespace PGMB\Mustache\Context\ThirdParty;


use PGMB\Mustache\Context\AbstractContextProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcfContext extends AbstractContextProvider {

	public function get_key(): string {
		return 'acf';
	}

	public function build(): array {
		return [];
	}

	public function describe(): array {
		return [
			'label' => __('ACF/SCF fields', 'post-to-google-my-business'),
			'is_premium' => true,
			'children' => [],
		];
	}
}