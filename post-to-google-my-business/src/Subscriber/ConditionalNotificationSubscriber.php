<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\NotificationManager;
use PGMB\PostTypes\SubPost;

class ConditionalNotificationSubscriber implements SubscriberInterface {

	const NOTIFICATION_SECTION = 'dashboard-notifications';

	/**
	 * @var NotificationManager
	 */
	private $notification_manager;

	public function __construct(NotificationManager $notification_manager) {

		$this->notification_manager = $notification_manager;
	}

	public static function get_subscribed_hooks() {
		return [
			"publish_mbp-google-subposts"   => 'trigger_review_notifications',
			'admin_notices'                 => 'show_welcome_message',
		];
	}

	/**
	 * Shows review notifications in the plugin dashboard
	 *
	 * @since 2.2.12
	 */
	public function trigger_review_notifications(){
		$posts_count = wp_count_posts(SubPost::POST_TYPE)->publish;

		if(!$posts_count == 7 || !$posts_count == 28 || !$posts_count == 100){ return; }

		$current_user = wp_get_current_user();

		$wordpress_repository_link = sprintf(
			'<a target="_blank" href="%s">%s</a>',
			'https://wordpress.org/support/plugin/post-to-google-my-business/reviews/#new-post',
			esc_html__( 'WordPress Repository', 'post-to-google-my-business' )
		);

		$plugin_developer = sprintf(
			'<strong>%s</strong><br /><i>%s</i>',
			'Koen',
			esc_html__( 'Plugin Developer', 'post-to-google-my-business' )
		);

		$you_deserve_it = sprintf(
			'<a target="_blank" href="%s"><strong>%s</strong></a>',
			'https://wordpress.org/support/plugin/post-to-google-my-business/reviews/#new-post',
			esc_html__( 'Ok, you deserve it', 'post-to-google-my-business' )
		);

		$maybe_later = sprintf(
			'<a class="mbp-notice-dismiss" href="#">%s</a>',
			esc_html__( 'Maybe later', 'post-to-google-my-business' )
		);

		$already_did = sprintf(
			'<a class="mbp-notice-dismiss" href="#" data-ignore="true">%s</a>',
			esc_html__( 'I already did', 'post-to-google-my-business' )
		);

		$user_display_name = esc_html($current_user->display_name);

		$avatar = 'img/koen.png';

		$alt = esc_html__( 'Developer profile photo', 'post-to-google-my-business' );

		if($posts_count == 7) {
			$review_notification = BasicNotification::create(
				self::NOTIFICATION_SECTION,
				'review_notification_7_posts',
				esc_html__( '7 posts!', 'post-to-google-my-business' ),
				nl2br( sprintf(
					esc_html__( "Hey %1\$s,\n\nI noticed you've just created your 7th GMB post through my plugin, awesome!\n\nIf you like the plugin and have a moment to leave a 5-star rating on the %2\$s, that really helps spread the word and move the development forwards!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s" ),
					$user_display_name,
					$wordpress_repository_link,
					$plugin_developer,
					$you_deserve_it,
					$maybe_later,
					$already_did
				) ),
				$avatar,
				$alt
			);
			$this->notification_manager->add_notification( $review_notification );
		}elseif($posts_count == 28){
			$review_notification = BasicNotification::create(
				self::NOTIFICATION_SECTION,
				'review_notification_28_posts',
				esc_html__('28 posts! Awesome', 'post-to-google-my-business'),
				nl2br(sprintf(
					esc_html__("Hi %1\$s,\n\nI hope you find my GMB plugin useful! I noticed you've already created 28 GMB posts with it.\n\nIf you like the plugin and have a moment to leave a 5-star rating on the %2\$s, that really helps boost my motivation!\n\nThanks!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s"),
					$user_display_name,
					$wordpress_repository_link,
					$plugin_developer,
					$you_deserve_it,
					$maybe_later,
					$already_did
				)),
				$avatar,
				$alt
			);
			$this->notification_manager->add_notification($review_notification);
		}elseif($posts_count == 100) {
			$review_notification = BasicNotification::create(
				self::NOTIFICATION_SECTION,
				'review_notification_100_posts',
				esc_html__('Wow! 100 posts!', 'post-to-google-my-business'),
				nl2br(sprintf(
					esc_html__("Hi %1\$s,\n\nYou've just published your 100th GMB post through the Post to Google My Business plugin, awesome!\n\nIf you find the plugin useful and have a moment to leave a 5-star rating on the %2\$s, you'd do me a BIG favour and it really motivates me to continue adding awesome features!\n\nDon't worry, this will be the last time I bother you about it.\n\nThanks!\n\n%3\$s\n\n%4\$s\n%5\$s\n%6\$s"),
					$user_display_name,
					$wordpress_repository_link,
					$plugin_developer,
					$you_deserve_it,
					$maybe_later,
					$already_did
				)),
				$avatar,
				$alt
			);
			$this->notification_manager->add_notification($review_notification);
		}
	}

	/**
	 * Shows a welcome notification to the user
	 *
	 * @since 2.2.12
	 */
	public function show_welcome_message(){
		$this->notification_manager->init();
		if(!get_option('pgmb_activated')){ return; }
		$current_user = wp_get_current_user();

		$anchor = _x('Google tab in the plugin settings', 'anchor text', 'post-to-google-my-business');
		$link = sprintf('<a href="%s">%s</a>', esc_url(admin_url('admin.php?page=pgmb_settings#mbp_google_settings')), $anchor);

		$welcome_message = BasicNotification::create(
			self::NOTIFICATION_SECTION,
			'welcome_message',
			esc_html__('Getting started with Post to Google My Business', 'post-to-google-my-business'),
			nl2br(sprintf(
				esc_html__("Hi %1\$s,\n\nThanks for installing Post to Google My Business! To get started, connect the plugin to your Google account on the %4\$s.\n\nNeed help? Check out the %2\$s\n\n%3\$s"),
				esc_html($current_user->display_name),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://tycoonmedia.net/gmb-tutorial-video/',
					esc_html__('tutorial video', 'post-to-google-my-business')
				),
				sprintf(
					'<strong>%s</strong><br /><i>%s</i>',
					'Koen',
					esc_html__('Plugin Developer', 'post-to-google-my-business')
				),
				$link
			)),
			'img/koen.png',
			esc_html__('Developer profile photo','post-to-google-my-business')
		);
		$this->notification_manager->add_notification($welcome_message);
		delete_option('pgmb_activated');
	}
}
