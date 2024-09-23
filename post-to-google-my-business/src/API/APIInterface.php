<?php


namespace PGMB\API;


use PGMB\Google\LocalPost;
use PGMB\Google\LocalPostEditMask;
use PGMB\Google\PublishedLocalPost;

interface APIInterface {
	public function get_accounts($flush_cache);
	public function get_locations($account_name, $flush_cache = false);
	public function get_location($location_name, $flush_cache = false);

	public function create_post($location_name, $post );


	public function delete_post($name);

	/**
	 * @param $post_id
	 * @param LocalPost $post
	 * @param LocalPostEditMask $mask
	 *
	 * @return PublishedLocalPost
	 */
	public function update_post($post_id, $post, LocalPostEditMask $mask );


	public function refresh_token();


	public function revoke_access();
}
