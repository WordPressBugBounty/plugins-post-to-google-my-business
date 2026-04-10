<?php

namespace PGMB\Mustache\Context;

class PostContext extends AbstractContextProvider {

	/**
	 * @var string
	 */


	public function __construct( int $post_id ) {
		parent::__construct( $post_id );
	}

	/**
	 * @inheritDoc
	 */
	public function get_key(): string {
		return 'post';
	}

	/**
	 * @inheritDoc
	 */
	public function build(): array {
		$post = $this->post();
		if (!$post) {
			return [];
		}

		return [
			'id'      => $post->ID,
			'title'   => get_the_title($post),
			'slug'    => $post->post_name,
			'content' => trim(apply_filters('the_content', $post->post_content)),
			'excerpt' => get_the_excerpt($post),
			'url'     => get_permalink($post),
			'date'    => get_the_date('', $post),
			'thumbnail'          => get_the_post_thumbnail_url($post),
			'author'  => [
				'id'   => $post->post_author,
				'name' => get_the_author_meta('display_name', $post->post_author),
			],
		];
	}

	public function describe(): array {
		return [
			'label' => __('WordPress post data', 'post-to-google-my-business'),
			'children' => [
				'id' => [
					'type'  => 'number',
					'label' => __('Post ID', 'post-to-google-my-business'),
					'example' => '123',
				],
				'title' => [
					'type'  => 'string',
					'label' => __('Post title', 'post-to-google-my-business'),
					'example' => 'Example post title',
				],
				'content' => [
					'type'  => 'string',
					'label' => __('Post content', 'post-to-google-my-business'),
				],
				'excerpt' => [
					'type'  => 'string',
					'label' => __('Post excerpt', 'post-to-google-my-business'),
				],
				'slug' => [
					'type'  => 'string',
					'label' => __('Post slug', 'post-to-google-my-business'),
					'example' => 'example-post-title-slug',
				],
				'url' => [
					'type'  => 'url',
					'label' => __('Post url', 'post-to-google-my-business'),
				],
				'date' => [
					'type'  => 'string',
					'label' => __('Post date', 'post-to-google-my-business'),
				],
				'thumbnail' => [
					'type'  => 'url',
					'label' => __('Post thumbnail URL', 'post-to-google-my-business'),
				],
				'author' => [
					'type' => 'object',
					'label' => 'Author',
					'children' => [
						'name' => [
							'type' => 'string',
							'label' => 'Post author name'
						]
					]
				]
			],
		];
	}
}