<?php


namespace PGMB\PostTypes;

use WP_Post;


/**
 * @method SubPost[] find()
 * @method SubPost find_one()
 * @method SubPost|null find_by_id( $id )
 */
class SubPostRepository extends AbstractRepository {

	const POST_TYPE = 'mbp-google-subposts';

//	public function order_by_post_type(){
//		$this->query = array_merge($this->query, [
//
//		]);
//
//		return $this;
//	}

	public function between(){
		$this->query = array_merge_recursive($this->query, [
			'meta_query' => [
				'relation' => 'AND',
			]
		]);
		return $this;
	}

	public function start_date($timestamp){
		$this->query = array_merge_recursive($this->query, [
			'meta_query' => [
				[
					'key'       => '_mbp_post_publish_date',
					'value'     => $timestamp,
					'compare'   => '>=',
					'type'      => 'DECIMAL'
				],
			]
		]);
		return $this;
	}

	public function end_date($timestamp){
		$this->query = array_merge_recursive($this->query, [
			'meta_query' => [
				[
					'key'       => '_mbp_post_publish_date',
					'value'     => $timestamp,
					'compare'   => '<=',
					'type'      => 'DECIMAL'
				],
			]
		]);
		return $this;
	}

	public function order_by_publish_date(){
		$this->query = array_merge($this->query, [
			'meta_key'			=> '_mbp_post_publish_date',
			'orderby'			=> 'meta_value',
		]);

		return $this;
	}

	public function order_by_creation_date(){
		$this->query = array_merge($this->query, [
			'orderby'   => 'date'
		]);

		return $this;
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return SubPost
	 */
	protected function load( WP_Post $post ) {
		return SubPost::from_post($post);
	}
}
