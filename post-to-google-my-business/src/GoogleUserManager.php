<?php

namespace PGMB;

use DateTime;

use Exception;
use PGMB\API\ProxyAuthenticationAPI;

use PGMB\BackgroundProcessing\AccountSyncQueueItem;
use PGMB\BackgroundProcessing\LocationSyncProcess;
use PGMB\Vendor\Firebase\JWT\JWK;
use PGMB\Vendor\Firebase\JWT\JWT;

class GoogleUserManager {

	/**
	 * @var ProxyAuthenticationAPI
	 */
	private $auth_api;
	/**
	 * @var \WP_Http
	 */
	private $transport;
	/**
	 * @var LocationSyncProcess
	 */
	private $location_sync_process;


	/**
	 * @throws Exception
	 */
	public function get_public_keys() {
		$transient = get_transient('pgmb_public_keys');
		if ($transient) {
			return $transient;
		}

		$response = $this->transport->get('https://app.posttogmb.com/google_cert');

		if (is_wp_error($response)) {
			throw new \Exception(sprintf(__('Unable to retrieve public keys from Google: %s', 'post-to-google-my-business'), esc_html($response->get_error_message())));
		}

		$http_code = wp_remote_retrieve_response_code($response);
		if ($http_code !== 200) {
			throw new \Exception(sprintf(
				__('Unexpected HTTP response code (%d) from Google: %s', 'post-to-google-my-business'),
				$http_code,
				'<pre>' . esc_html(print_r($response, true)) . '</pre>'
			));
		}

		$expires_header = wp_remote_retrieve_header($response, 'expires');
		if (!$expires_header) {
			throw new \Exception(__('Missing "expires" header in Google response.', 'post-to-google-my-business'));
		}

		try {
			$expires = new DateTime($expires_header);
			$now = new DateTime();
			$expires_in_seconds = max(0, $expires->getTimestamp() - $now->getTimestamp() - 20); // Subtract 20s for safety
		} catch (Exception $e) {
			throw new \Exception(__('Invalid "expires" header format in Google response.', 'post-to-google-my-business'));
		}

		$keys = json_decode(wp_remote_retrieve_body($response), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception(sprintf(
				__('Failed to parse public key JSON from Google response: %s', 'post-to-google-my-business'),
				'<pre>' . esc_html(json_last_error_msg()) . '</pre>'
			));
		}

		if (empty($keys['keys'])) {
			throw new \Exception(__('Google response is missing expected "keys" data.', 'post-to-google-my-business'));
		}

		set_transient('pgmb_public_keys', $keys, $expires_in_seconds);

		return $keys;
	}

	public function __construct(ProxyAuthenticationAPI $auth_api, \WP_Http $transport, LocationSyncProcess $location_sync_process){
		$this->auth_api = $auth_api;
		$this->transport = $transport;
		$this->location_sync_process = $location_sync_process;
	}

	private function clear_tokens($account_id, $account){
		//This will actually revoke all tokens on all websites connected to the account which is not what we want
//		try {
//			$this->auth_api->revoke_refresh_token($account['refresh_token']);
//		}catch(\Exception $e){
//			error_log(sprintf('Failed to revoke access token for account ID %s: %s', $account['email'], $e->getMessage()));
//		}
		$this->auth_api->clear_access_token_cache($account_id);
	}

	public function delete_account($account_id){
		$accounts = get_option('pgmb_accounts');
		if(!is_array($accounts) || !array_key_exists($account_id, $accounts)){
			return;
		}

		$this->clear_tokens($account_id, $accounts[$account_id]);

		unset($accounts[$account_id]);

		update_option('pgmb_accounts', $accounts);
	}

	public function delete_all_accounts(){
		$accounts = get_option('pgmb_accounts');
		if(!is_array($accounts)){
			return;
		}

		foreach($accounts as $account_id => $account){
			$this->clear_tokens($account_id, $account);
		}


		delete_option('pgmb_accounts');
	}

	public function add_account($tokens){
		$keys = $this->get_public_keys();

		JWT::$leeway = 60;

		$account_data = JWT::decode( $tokens->id_token, JWK::parseKeySet( (array) $keys ), [ 'RS256' ] );

		$scopes = explode(" ", $tokens->scope);
		if(!in_array('https://www.googleapis.com/auth/business.manage', $scopes)){
			throw new Exception(__('You did not give the plugin permission to manage your Google Business Profile listings. The plugin will not work without this permission. Please retry the authentication and make sure you grant the plugin permission to manage your locations.', 'post-to-google-my-business'));
		}
		//$tokens->scope = https://www.googleapis.com/auth/userinfo.profile openid https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/business.manage
		$accounts = get_option('pgmb_accounts');
		if(!$accounts){$accounts = [];}

		$accounts[$account_data->sub] = [
			'name'          => $account_data->name,
			'email'         => sanitize_email($account_data->email),
			'owner'         => get_current_user_id(),
			'refresh_token' => $tokens->refresh_token
		];

		update_option('pgmb_accounts', $accounts);

		$this->auth_api->set_access_token($account_data->sub, $tokens->access_token, $tokens->expires_in - 20);
		$this->location_sync_process->push_to_queue(new AccountSyncQueueItem($account_data->sub))->save()->dispatch();
		/*
		 * stdClass Object
			(
			    [iss] => https://accounts.google.com
			    [azp] => 12345.apps.googleusercontent.com
			    [aud] => 12345.apps.googleusercontent.com
			    [sub] => 123456789
			    [hd] => koenreus.com
			    [email] => ik@koenreus.com
			    [email_verified] => 1
			    [at_hash] => 123456
			    [name] => Koen Reus
			    [picture] => https://lh3.googleusercontent.com/a-/123456
			    [given_name] => Koen
			    [family_name] => Reus
			    [locale] => en-GB
			    [iat] => 1619010057
			    [exp] => 1619013657
			)
		 */
		return $account_data->sub;
	}

	public function get_accounts(){
		$accounts = get_option('pgmb_accounts');
		if(!is_array($accounts)){
			return false;
		}

		return $accounts;
	}

	public function get_account($account_id){
		$accounts = $this->get_accounts();
		if(empty($accounts[$account_id])){ return false;}
		return $accounts[$account_id];
	}
}
