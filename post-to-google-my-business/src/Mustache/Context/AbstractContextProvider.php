<?php

namespace PGMB\Mustache\Context;

use PGMB\Mustache\MustacheContextProviderInterface;
use WP_Post;

abstract class AbstractContextProvider implements MustacheContextProviderInterface {
	protected $post_id;

	public function __construct(int $post_id){
		$this->post_id = $post_id;
	}

	public function post(): ?WP_Post{
		return get_post($this->post_id);
	}
}