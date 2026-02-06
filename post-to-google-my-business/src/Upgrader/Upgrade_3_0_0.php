<?php


namespace PGMB\Upgrader;


use PGMB\Admin\DashboardPage;
use PGMB\API\ProxyAuthenticationAPI;
use PGMB\GoogleUserManager;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\FeatureNotification;
use PGMB\Notifications\NotificationManager;
use PGMB\Plugin;
use PGMB\PostTypes\GooglePostEntity;
use PGMB\PostTypes\GooglePostEntityRepository;
use PGMB\PostTypes\SubPost;

use PGMB\Vendor\TypistTech\WPAdminNotices\AbstractNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\StickyNotice;
use PGMB\Vendor\TypistTech\WPAdminNotices\Store;
use Exception;

class Upgrade_3_0_0 implements DistributedUpgrade {

	private $upgrader;
	/**
	 * @var Store
	 */
	private $notice_store;
	/**
	 * @var GooglePostEntityRepository
	 */
	private $entity_repository;


	private $sub_id;

	/**
	 * @var ProxyAuthenticationAPI
	 */
	private $authentication_API;
	/**
	 * @var GoogleUserManager
	 */
	private $user_manager;
	/**
	 * @var NotificationManager
	 */
	private $notification_manager;


	private function extract_sub_id_from_name($name){
		if(!is_string($name)){return false;}
		preg_match('/\d+/', $name, $matches);

		if(!isset($matches[0])){ return false; }

		return $matches[0];
	}

	public function __construct(Store $notice_store, GooglePostEntityRepository $entity_repository, ProxyAuthenticationAPI $authentication_API, GoogleUserManager $user_manager, NotificationManager $notification_manager) {
		$this->notice_store = $notice_store;
		$this->entity_repository = $entity_repository;
		$this->authentication_API = $authentication_API;

		$this->user_manager = $user_manager;
		$this->notification_manager = $notification_manager;
	}

	public function display_reconnect_notice(){
		$anchor = _x('Google account settings', 'anchor text', 'post-to-google-my-business');
		$link = sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=pgmb_settings#mbp_google_settings')), $anchor);
		/* translators: %s is link to Google account settings */
		$reconnect_notice = new StickyNotice('reconnect_notice', '<p>'.sprintf(__('Thanks for updating Post to Google My Business! Due to major changes in the Google account management system, re-authenticating might be required. Check the %s and reconnect your account if needed.', 'post-to-google-my-business'), $link).'</p>', AbstractNotice::INFO);
		$this->notice_store->add($reconnect_notice);
	}

	public function run() {
		$this->display_reconnect_notice();

		$this->fetch_old_refresh_token();

		$this->queue_post_updates();

		$this->update_default_location();

		$this->update_default_template_location();

		$this->delete_unused_options();

		$this->add_update_notifications();

		Plugin::activate_single_site(true);
	}

	private function delete_unused_options(){
		//The state of "ignored" notification is now saved in generic option mbp_ignored_notifications
		delete_option('mbp_review_notifications');
		delete_option('mbp_welcome_message');

		//options for old API
		delete_option('mbp_request_key');
		delete_option('mbp_api_key');
		delete_option('mbp_api_token');
		delete_option('mbp_site_key');

		//Options for the deleted debug info page
		delete_option('mbp_debug_info');

		//Settings option for former Dashboard page
		delete_option('mbp_dashboard');

		//Delete unused hooks
		if(function_exists('wp_unschedule_hook')){
			wp_unschedule_hook('mbp_refresh_token');
		}
	}

	private function queue_post_updates(){
		$subposts = get_posts([
			'numberposts'       => -1,
			'post_type'         => SubPost::POST_TYPE,
			'fields'            => 'ids',
		]);
		foreach($subposts as $post_id){
			$this->upgrader->push_to_queue($post_id, [$this, 'process_post_update']);
		}

		$this->upgrader->save()->dispatch();
	}

	private function fetch_old_refresh_token(){
		$old_api_url = 'https://util.tycoonmedia.net/api/v1/google/get_refresh_token';
		$site_key = get_option('mbp_site_key');
		$token 	= get_option('mbp_api_token');
		if(!$site_key || !$token){
			return;
		}

		$response = wp_remote_get(add_query_arg([
			'apiKey' => $site_key,
			'token'  => $token,
		], $old_api_url), [
			'timeout'   => 20,
		]);
		if(is_wp_error($response)){
			return;
		}
		try{
			$data = json_decode(wp_remote_retrieve_body($response));

			$new_tokens = $this->authentication_API->access_token_from_refresh_token($data->refresh_token);
			$new_tokens->refresh_token = $data->refresh_token;

			$this->sub_id = $this->user_manager->add_account($new_tokens);
		}catch(Exception $e){

			return;
		}
	}

	public function process_post_update($post_id){
		$created_posts = get_post_meta($post_id, 'mbp_posts', true);
		$post_errors = get_post_meta($post_id, 'mbp_errors', true);

		if(!empty($created_posts) || !empty($post_errors)){
			$old_entities = array_merge($post_errors, $created_posts);
			$last_error = false;
			foreach($old_entities as $location => $item){
				$sub_id = $this->sub_id ?: $this->extract_sub_id_from_name($location) ?: 'updated_no_account';
				$new_entity = GooglePostEntity::from_api($sub_id, $location);
				$new_entity->set_post_parent($post_id);

				if(!is_wp_error($item)){
					$new_entity->set_post_success($item['name'], $item['state'], $item['searchUrl']);
				}else{
					$new_entity->set_post_failure($item->get_error_message());
					$last_error = $item->get_error_message();
				}
				$this->entity_repository->persist($new_entity);
			}
			update_post_meta($post_id, 'mbp_last_error', $last_error);
		}

		$this->update_post_locations($post_id);

		$this->update_location_in_template($post_id);

		delete_post_meta($post_id, 'mbp_posts');
		delete_post_meta($post_id, 'mbp_errors');
	}

	private function update_default_location(){
		$google_settings = get_option('mbp_google_settings');
		if(!$google_settings || !isset($google_settings['google_location'])){
			return;
		}

		$old_default_location = $google_settings['google_location'];

		$sub_id = $this->sub_id ?: $this->extract_sub_id_from_name($google_settings['google_location']) ?: false;
		if(!$sub_id){ return; }

		$google_settings['google_location'] = [
			$sub_id => $old_default_location,
		];
		update_option('mbp_google_settings', $google_settings);
	}

	private function update_post_locations($post_id){
		$form_fields = get_post_meta($post_id, 'mbp_form_fields', true);
		if(!$form_fields){ return; }

		update_post_meta($post_id, 'mbp_form_fields', $this->update_location_in_form_fields($form_fields));
	}

	private function update_location_in_template($post_id){
		$template = get_post_meta($post_id, '_mbp_autopost_template', true);
		if(!$template){ return; }
		update_post_meta($post_id, '_mbp_autopost_template', $this->update_location_in_form_fields($template));
	}

	private function update_location_in_form_fields($form_fields){
		if(!isset($form_fields['mbp_selected_location'])){ return $form_fields; }
		if(is_array($form_fields['mbp_selected_location']) && !empty($form_fields['mbp_selected_location'])){
			$locations = $form_fields['mbp_selected_location'];

			$sub_id = $this->sub_id ?: $this->extract_sub_id_from_name($form_fields['mbp_selected_location'][0]) ?: false;
			if($sub_id){
				$form_fields['mbp_selected_location'] = [
					$sub_id => $locations
				];
			}
		}elseif(!is_array($form_fields['mbp_selected_location']) && !empty($form_fields['mbp_selected_location'])){
			$locations = $form_fields['mbp_selected_location'];
			$sub_id = $this->sub_id ?: $this->extract_sub_id_from_name($form_fields['mbp_selected_location']) ?: false;
			if($sub_id){
				$form_fields['mbp_selected_location'] = [
					$sub_id => [$locations]
				];
			}
		}
		return $form_fields;
	}

	private function add_update_notifications(){
		$multi_account_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_0_0_multi_account',
			esc_html__('[Agency] Multi-account support', 'post-to-google-my-business'),
			esc_html__('Connect multiple Google accounts to a single site, and publish posts across multiple GBP locations & Google accounts in one go.', 'post-to-google-my-business'),
			'img/features/3_0_0_multi_account.png',
			''
		);
		$this->notification_manager->add_notification($multi_account_feature);

		$evergeen_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_0_0_evergreen',
			esc_html__('[Pro] Evergreen content', 'post-to-google-my-business'),
			esc_html__('Select your pre-existing content in bulk and have the plugin automatically publish random items from your selection at a schedule you define', 'post-to-google-my-business'),
			'img/features/3_0_0_evergreen_content.png',
			''
		);
		$this->notification_manager->add_notification($evergeen_feature);

		$autopost_template_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_0_0_autopost',
			esc_html__('[Pro] Multiple auto-post templates', 'post-to-google-my-business'),
			esc_html__('Instead of one generic auto-post template, you can now create multiple templates for different occasions.', 'post-to-google-my-business'),
			'img/features/3_0_0_autopost_templates.png',
			''
		);
		$this->notification_manager->add_notification($autopost_template_feature);

		$product_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_0_0_products',
			esc_html__('[All Premium] Product support', 'post-to-google-my-business'),
			esc_html__('Publish your latest (WooCommerce) products to your GBP listing. The plugin now supports "real" products in GBP!', 'post-to-google-my-business'),
			'img/features/3_0_0_products.png',
			''
		);
		$this->notification_manager->add_notification($product_feature);

		$pagination_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_0_0_pagination',
			esc_html__('Bulk actions & Pagination', 'post-to-google-my-business'),
			esc_html__('The post list is now cleaner, faster & more versatile with the newly added pagination and bulk actions.', 'post-to-google-my-business'),
			'img/features/3_0_0_pagination.png',
			''
		);
		$this->notification_manager->add_notification($pagination_feature);

		$current_user = wp_get_current_user();
		$notification = BasicNotification::create(
			DashboardPage::NOTIFICATION_SECTION,
			'3_0_0_upgrade_notification',
			esc_html__('Thanks for updating Post to Google My Business!', 'post-to-google-my-business'),
			nl2br(sprintf(
				/* translators: %1$s is display name/username, %2$s is link to leave a review (anchor: "leave a rating"), %3$s is plugin developer name (Koen) + "Plugin Developer" title  */
				esc_html__("Hey %1\$s,\n\nThanks for updating Post to Google My Business to version 3, the \"big update\"! The plugin received a massive overhaul from a technical standpoint, making it faster, more stable & more intuitive.\n\nI've also added a ton of features that you've all been asking for. I've highlighted the major ones in the \"New features\" section, which you can check out below!\n\nIf you like the plugin and have a moment to %2\$s, that's much appreciated and really helps me move the plugin forward.\n\n%3\$s", 'post-to-google-my-business'),
				esc_html($current_user->display_name),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://wordpress.org/plugins/post-to-google-my-business/',
					esc_html__('leave a rating', 'post-to-google-my-business')
				),
				sprintf(
					'<strong>%s</strong><br /><i>%s</i>',
					'Koen',
					esc_html__('Plugin Developer', 'post-to-google-my-business')
				)
			)),
			'img/koen.png',
			esc_html__('Developer profile photo','post-to-google-my-business')
		);
		$this->notification_manager->add_notification($notification);

	}

	private function update_default_template_location(){
		$autopost_settings = get_option('mbp_quick_post_settings');
		if(!$autopost_settings || !isset($autopost_settings['autopost_template']['mbp_selected_location'])){
			return;
		}

		update_option('mbp_quick_post_settings', $this->update_location_in_form_fields($autopost_settings));
	}

	public function set_background_process( UpgradeBackgroundProcess $upgrader ) {
		$this->upgrader = $upgrader;
	}
}
