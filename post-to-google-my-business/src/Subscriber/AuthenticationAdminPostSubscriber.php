<?php


namespace PGMB\Subscriber;


use Exception;
use PGMB\API\ProxyAuthenticationAPI;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\GoogleUserManager;
use PGMB\Vendor\Firebase\JWT\BeforeValidException;
use PGMB\Vendor\Firebase\JWT\ExpiredException;


class AuthenticationAdminPostSubscriber implements SubscriberInterface {

	/**
	 * @var ProxyAuthenticationAPI
	 */
	private $auth_api;
	/**
	 * @var GoogleUserManager
	 */
	private $user_manager;

	public function __construct(ProxyAuthenticationAPI $auth_api, GoogleUserManager $user_manager){

		$this->auth_api = $auth_api;
		$this->user_manager = $user_manager;
	}

	/**
	 * @uses generate_url()
	 * @uses unlink_site()
	 * @uses revoke_access()
	 * @uses fetch_tokens()
	 */
	public static function get_subscribed_hooks() {
		return [
			'admin_post_mbp_generate_url'           => 'generate_url',
			'admin_post_mbp_disconnect'             => 'unlink_site',
			'admin_post_mbp_revoke'                 => 'revoke_access',
			'admin_post_pgmb_google_authorized'     => 'fetch_tokens',
			'admin_post_pgmb_auth_failed'           => 'auth_failed',
		];
	}

	protected function wp_die_args(){
		return [
			'link_url'  => 	esc_url(admin_url('admin-post.php?action=mbp_generate_url')),
			'link_text' => __('Retry', 'post-to-google-my-business'),
		];
	}

	public function generate_url(){
		if(!current_user_can('pgmb_manage_google_accounts')){
			wp_die(__('You do not have permission to add Google accounts', 'post-to-google-my-business'),'', $this->wp_die_args());
		}

		try{
			$response = $this->auth_api->get_authentication_url(esc_url(admin_url('admin-post.php')), wp_create_nonce('mbp_generate_url'));
		}catch(\Exception $e){
			wp_die(sprintf(__('Could not generate authentication URL: %s', 'post-to-google-my-business'), $e->getMessage()),'', $this->wp_die_args());
		}

		wp_redirect($response->url);
		exit;
	}

	public function unlink_site(){
		if(!current_user_can('pgmb_manage_google_accounts') || !current_user_can('pgmb_see_others_accounts')){
			wp_die(__('You do not have permission to manage connected Google accounts', 'post-to-google-my-business'),'', $this->wp_die_args());
		}

		$this->user_manager->delete_all_accounts();

		//Clear the saved settings
		update_option('mbp_google_settings', false);

		wp_safe_redirect(admin_url('admin.php?page=pgmb_settings#mbp_google_settings'));
		exit;
	}



	public function fetch_tokens(){
		if(!wp_verify_nonce(sanitize_key($_REQUEST['state']), 'mbp_generate_url')){ wp_die(__('Invalid nonce', 'post-to-google-my-business'),'', $this->wp_die_args()); }

		if(!current_user_can('pgmb_manage_google_accounts')){
			wp_die(__('You do not have permission to add Google accounts', 'post-to-google-my-business'),'', $this->wp_die_args());
		}

		if(empty($_REQUEST['code'])){ wp_die(__('Did not receive authentication code', 'post-to-google-my-business'),'', $this->wp_die_args()); }

		try{
			$tokens = $this->auth_api->get_tokens_from_code($_REQUEST['code']);
		}catch(Exception $e){
			wp_die(sprintf(__('Could not obtain access tokens: %s', 'post-to-google-my-business'), $e->getMessage()), '', $this->wp_die_args());
		}

		try {
			$this->user_manager->add_account($tokens);
		}catch ( ExpiredException $e){
		}catch( BeforeValidException $e){
			wp_die(sprintf(__('Could not verify Google access token: %s. Is the date & time on your server set correctly?', 'post-to-google-my-business'), $e->getMessage()),'', $this->wp_die_args());
		}catch( Exception $e){
			wp_die(sprintf(__('Could not verify Google access token: %s', 'post-to-google-my-business'), $e->getMessage()),'', $this->wp_die_args());
		}


		wp_safe_redirect(admin_url('admin.php?page=pgmb_settings#mbp_google_settings'));
		exit;
	}

	/**
	 * When the auth request fails, for example when the user presses the cancel button on the Google dialog
	 */
	public function auth_failed(){
		if(!wp_verify_nonce(sanitize_key($_REQUEST['state']), 'mbp_generate_url')){ wp_die(__('Invalid nonce', 'post-to-google-my-business'),'', $this->wp_die_args()); }

		$reason = '';

		if(!empty($_REQUEST['error'])){
			switch($_REQUEST['error']){
				case 'access_denied':
					$reason = __('The request was cancelled', 'post-to-google-my-business');
			}
		}

		wp_die(sprintf(__('The authorization failed: %s', 'post-to-google-my-business'), $reason),'', $this->wp_die_args());
	}
}
