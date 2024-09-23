<?php


namespace PGMB\PostTypes;


use WP_Post;

/**
 * @method GooglePostEntity[] find()
 * @method GooglePostEntity find_one()
 */
class GooglePostEntityRepository extends AbstractRepository {

	const POST_TYPE = 'mbp_post_entity';

	/**
	 * Prefix for the meta fields
	 */
	const META_PREFIX = '_pgmb';

	public function find_by_user_key($user_key){
		$this->query = array_merge_recursive($this->query, [
			'meta_query' => [
				[
					'key'   => self::META_PREFIX.'_user_key',
					'value' => $user_key
				]
			]
		]);

		return $this;
	}

	public function find_by_location($location){
		$this->query = array_merge_recursive($this->query, [
			'meta_query' => [
				[
					'key'   => self::META_PREFIX.'_location_id',
					'value' => $location
				]
			]
		]);
		return $this;
	}


	/**
	 * Return a PostEntity object from the supplied WP_Post instance by
	 * mapping post meta fields and WP_Post properties to PostEntity properties
	 *
	 * @param WP_Post $post
	 *
	 * @return GooglePostEntity
	 */
	protected function load( WP_Post $post ){
		return GooglePostEntity::from_post($post);
	}

}
