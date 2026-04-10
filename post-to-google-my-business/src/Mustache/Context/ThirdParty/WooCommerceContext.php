<?php

namespace PGMB\Mustache\Context\ThirdParty;


use PGMB\Mustache\Context\AbstractContextProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerceContext extends AbstractContextProvider {

	/**
	 * @inheritDoc
	 */
	public function get_key(): string {
		return 'wc';
	}

	/**
	 * @inheritDoc
	 */
	public function build(): array {
		return [];
	}

	public function describe(): array {
		return [
			'label' => __('WooCommerce product & shop data', 'post-to-google-my-business'),
			'is_premium' => true,
			'children' => []
		];
	}
}