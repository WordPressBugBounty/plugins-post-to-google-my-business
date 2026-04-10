<?php

namespace PGMB\Mustache\Context;


use PGMB\Mustache\MustacheContextProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SiteContext implements MustacheContextProviderInterface {

	/**
	 * @inheritDoc
	 */
	public function get_key(): string {
		return 'site';
	}

	/**
	 * @inheritDoc
	 */
	public function build(): array {
		$site_variables = array('name', 'description', 'url', 'pingback_url', 'atom_url', 'rdf_url', 'rss_url', 'rss2_url', 'comments_atom_url', 'comments_rss2_url');
		$variables = [];
		foreach($site_variables as $variable){
			$variables[$variable] = get_bloginfo($variable);
		}
		return $variables;
	}

	public function describe(): array {
		return [
			'label' => __('Website details', 'post-to-google-my-business'),
			'children' => [
				'name' => [
					'type' => 'string',
					'label' => __('Website name', 'post-to-google-my-business'),
				],
				'description' => [
					'type' => 'string',
					'label' => __('Website description', 'post-to-google-my-business'),
				],
				'url' => [
					'type' => 'url',
					'label' => __('Website url', 'post-to-google-my-business'),
				],
			],
		];
	}
}