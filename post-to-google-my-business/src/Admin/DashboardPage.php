<?php

namespace PGMB\Admin;

use PGMB\Notifications\NotificationManager;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;

class DashboardPage extends AbstractPage implements EnqueuesScriptsInterface, AjaxCallbackInterface {

	const NOTIFICATION_SECTION = 'dashboard-notifications';
	const NEW_FEATURES_SECTION = 'feature-notifications';


	/**
	 * @var NotificationManager
	 */
	public $notification_manager;
	private $plugin_version;


	public function __construct($template_path, $plugin_version, $plugin_url, NotificationManager $notification_manager){
        parent::__construct($template_path, $plugin_url);
		$this->notification_manager = $notification_manager;
		$this->plugin_version = $plugin_version;
	}

	public function enqueue_scripts(){
		add_thickbox();
		wp_enqueue_script(
			'pgmb-dashboard',
			$this->plugin_url . 'js/dashboard.js',
			['jquery'],
			$this->plugin_version,
			true
		);
		wp_localize_script('pgmb-dashboard', 'pgmb_dashboard_data',[
			'calendar_timezone' => WpDateTimeZone::getWpTimezone()->getName(),
            'nonce'             => wp_create_nonce('dashboard_nonce'),
            'calendar_nonce'    => wp_create_nonce('calendar_nonce'),
            'locale'   => get_locale(),
            'delete_nonce'      => wp_create_nonce('mbp_post_nonce'), //Todo: global/same nonce
		]);
	}

	public function get_notification_count_html(){
		if(mbp_fs()->is_in_trial_promotion()){
			return '';
		}
		$count = $this->notification_manager->notification_count('dashboard-notifications');
		if($count >= 1){
			return '<span class="update-plugins"><span class="update-count">'.$count.'</span></span>';
		}
		return '';
	}

	public function get_page_title(){
		return __('Post to Google My Business Dashboard', 'post-to-google-my-business');
	}

	public function get_menu_title(){
		return sprintf(__('Dashboard %s', 'post-to-google-my-business'), $this->get_notification_count_html());
	}

	public function render_page(){
		include($this->template_path.'dashboard.php');
	}

    public function ajax_delete_notification(){
        if(!current_user_can('manage_options') || !wp_verify_nonce($_REQUEST['nonce'], 'dashboard_nonce')){
            wp_send_json_error();
        }
	    $identifier = sanitize_key($_REQUEST['identifier']);
	    $section = sanitize_key($_REQUEST['section']);
	    $ignore = isset($_REQUEST['ignore']) ? json_decode($_REQUEST['ignore']) : false;
	    $this->notification_manager->delete_notification($section, $identifier, $ignore);
	    wp_send_json_success();
    }



	public function get_notifications(){
		foreach($this->notification_manager->get_notifications(self::NOTIFICATION_SECTION) as $identifier => $data){
			$notification = new \PGMB\Notifications\BasicNotification(self::NOTIFICATION_SECTION, $identifier, $data);
			?>
			<div class="pgmb-message pgmb-notification" data-section="<?php echo self::NOTIFICATION_SECTION; ?>" data-identifier="<?php echo $identifier; ?>">
				<button type="button" class="notice-dismiss mbp-notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "post-to-google-my-business");?></span></button>
				<img src="<?php echo $this->plugin_url.$notification->get_image(); ?>" alt="<?php echo $notification->get_alt(); ?>" />
				<h3><?php echo $notification->get_title(); ?></h3>
				<?php echo $notification->get_text(); ?>
				<div class="clear"></div>
			</div>
			<?php
		}
	}

	public function get_new_features(){
		foreach($this->notification_manager->get_notifications(self::NEW_FEATURES_SECTION) as $identifier => $data){
			$new_feature = new \PGMB\Notifications\FeatureNotification(self::NEW_FEATURES_SECTION, $identifier, $data);
			?>
			<div class="pgmb-message pgmb-new-feature" data-section="<?php echo self::NEW_FEATURES_SECTION; ?>" data-identifier="<?php echo $identifier; ?>">
				<button type="button" class="notice-dismiss mbp-notice-dismiss"><span class="screen-reader-text"><?php _e("Dismiss this notice.", "post-to-google-my-business");?></span></button>
				<h3><?php echo $new_feature->get_title(); ?></h3>
				<img src="<?php echo $this->plugin_url.$new_feature->get_image(); ?>" alt="<?php echo $new_feature->get_alt(); ?>" />
				<?php echo $new_feature->get_text(); ?>
			</div>
			<?php
		}
	}

	public function get_position() {
		return 0;
	}

	public function ajax_callbacks() {
		return [
            'mbp_delete_notification' => [$this, 'ajax_delete_notification']
        ];
	}

	public function get_menu_slug() {
        return 'post_to_google_my_business';
	}
}
