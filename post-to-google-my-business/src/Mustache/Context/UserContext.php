<?php

namespace PGMB\Mustache\Context;


use PGMB\Mustache\MustacheContextProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserContext extends AbstractContextProvider {

	/**
	 * @inheritDoc
	 */
	public function get_key(): string {
		return 'author';
	}

	/**
	 * @inheritDoc
	 */
	public function build(): array {
		$post = $this->post();
		if (!$post) {
			return [];
		}
		$variables = [];
		//User info
		$user_variables = array(
			'aim',
			'description',
			'display_name',
			'first_name',
			'jabber',
			'last_name',
			'nickname',
			'user_email',
			'user_nicename',
			'user_url',
			'yim'
		);
		foreach($user_variables as $variable){
			$variables[$variable] = get_the_author_meta($variable, $post->post_author);
		}
		return $variables;
	}

	public function describe(): array {
		return [
			'label' => __('Post author data', 'post-to-google-my-business'),
			'children' => [
				'user_email' => [
					'type' => 'string',
					'label' => 'User email',
				],
				'user_description' => [
					'type' => 'string',
					'label' => 'User description',
				],
			],
		];
	}
}