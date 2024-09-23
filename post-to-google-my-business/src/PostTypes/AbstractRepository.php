<?php


namespace PGMB\PostTypes;


use WP_Post;


/**
 * Concrete class should add "@method" definitions for find() and find_one() methods' return type.
 */
abstract class AbstractRepository implements \Countable {
	/**
	 * @var \WP_Query
	 */
	protected $WP_query;

	protected $query = [];

	public function __construct(\WP_Query $WP_query){
		$this->WP_query = $WP_query;
	}

	/**
	 * @param int $parent_id
	 *
	 * @return $this
	 */
	public function find_by_parent($parent_id){
		$this->query = array_merge($this->query, [
			'post_parent'   => $parent_id,
		]);
		return $this;
	}

	/**
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit($limit = 10){
		$this->query = array_merge($this->query, [
			'posts_per_page'    => (int)$limit
		]);
		return $this;
	}

	public function offset($offset = 0){
		$this->query = array_merge($this->query, [
			'offset'    => (int)$offset
		]);
		return $this;
	}

	public function count() : int {
		return $this->WP_query->found_posts;
	}

	/**
	 * Find a collection of post entities
	 *
	 * @return EntityInterface[]
	 */
	public function find(){
		$query = array_merge([
			'post_type' => static::POST_TYPE,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		], $this->query);
		$items = $this->WP_query->query($query);
		$this->query = [];
		return array_map([$this, 'load'], $items);
	}

	public function delete(EntityInterface $entity){
		wp_delete_post($entity->get_id(), true);
	}

	/**
	 * Stores EntityInterface compatible entity in the WP database
	 *
	 * @param EntityInterface $post
	 *
	 * @return int|\WP_Error
	 */
	public function persist(EntityInterface $post){
		return wp_insert_post($post->get_post_data(), true);
	}

	public function asc(){
		$this->query = array_merge($this->query, [
			'order'   => 'ASC'
		]);

		return $this;
	}

	public function desc(){
		$this->query = array_merge($this->query, [
			'order'   => 'DESC'
		]);

		return $this;
	}

	public function find_by_id($id){
		$this->query = array_merge($this->query, [
			'p'    => (int)$id
		]);

		return $this->find_one();
	}

	/**
	 * Find a single post entity
	 *
	 * @return EntityInterface|null
	 */
	public function find_one(){
		$this->query = array_merge($this->query, [
			'posts_per_page'    => 1,
			'no_found_rows' => true,
		]);
		$items = $this->find();
		return !empty($items[0]) ? $items[0] : null;
	}

	abstract protected function load( WP_Post $post );
}
