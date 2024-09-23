<?php

namespace PGMB\REST;

use WP_Error;

class GetAccountsRoute implements RouteInterface {

	public function get_endpoint_name(): string {
		return 'accounts';
	}

	public function get_methods(): string {
		return 'GET';
	}

	public function callback(){
		$accounts = get_option('pgmb_accounts');
		if(!$accounts || !is_array($accounts)){
			rest_ensure_response(new WP_Error(null, __('Could not load account data', 'post-to-google-my-business')));
		}

		if(!current_user_can('pgmb_see_others_accounts')){
			foreach($accounts as $id => $account){
				if(isset($account['owner']) && $account['owner'] != get_current_user_id()){
					unset($accounts[$id]);
				}
			}
		}

		if(empty($accounts)){
			return new \WP_REST_Response($accounts);
		}

		$accounts = apply_filters('mbp_business_selector_google_accounts', $accounts);

		return new \WP_REST_Response($accounts);
	}

	public function permission_callback(): bool {
		return is_user_logged_in();
	}
}