<?php

namespace PGMB\Upgrader;

use PGMB\Admin\DashboardPage;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\FeatureNotification;
use PGMB\Notifications\NotificationManager;

class Upgrade_3_4_0 implements Upgrade {

	private NotificationManager $notification_manager;

	public function __construct(NotificationManager $notification_manager){

		$this->notification_manager = $notification_manager;
	}

	public function run() {

		$mustache_feature = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_4_0_mustache',
			esc_html__('Dynamic templates', 'post-to-google-my-business'),
			esc_html__('Build flexible auto-post templates using dynamic data from your site. Set it once and let it update itself.', 'post-to-google-my-business'),
			'img/features/3_4_0_mustache.png',
			''
		);
		$this->notification_manager->add_notification($mustache_feature);


		$post_preview = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_4_0_post_preview',
			esc_html__('Live preview', 'post-to-google-my-business'),
			esc_html__('Preview your post or template before publishing, so you know exactly what will be sent to Google.', 'post-to-google-my-business'),
			'img/features/3_4_0_post_preview.png',
			''
		);
		$this->notification_manager->add_notification($post_preview);


		$variable_selector = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_4_0_variable_selector',
			esc_html__('Variable browser', 'post-to-google-my-business'),
			esc_html__('Browse and insert available data from your site without guessing variable names.', 'post-to-google-my-business'),
			'img/features/3_4_0_variable_selector.png',
			''
		);
		$this->notification_manager->add_notification($variable_selector);


		$custom_fields = FeatureNotification::create(
			DashboardPage::NEW_FEATURES_SECTION,
			'3_4_0_custom_fields',
			esc_html__('Custom fields support', 'post-to-google-my-business'),
			esc_html__('Use data from custom fields directly in your posts, including support for ACF, SCF and WooCommerce.', 'post-to-google-my-business'),
			'img/features/3_4_0_custom_fields.png',
			''
		);
		$this->notification_manager->add_notification($custom_fields);


		$current_user = wp_get_current_user();

		$notification = BasicNotification::create(
			DashboardPage::NOTIFICATION_SECTION,
			'3_4_0_upgrade_notification',
			esc_html__('Templates just got a big upgrade', 'post-to-google-my-business'),
			nl2br(sprintf(
			/* translators: %1$s is display name, %2$s is docs link, %3$s is review link, %4$s is developer signature */
				esc_html__(
					"Hey %1\$s,\n\nTemplates in Post to Google My Business just got a big upgrade.\n\nIf you've used the %%variable%% system before, this builds on top of that with a more flexible approach. You can now structure your templates more freely and pull in data from posts, custom fields or WooCommerce in a much cleaner way.\n\nTo make this easier to use, there's also a new variable browser and a live preview.\n\nThe old %%variable%% system still works, but this gives you much more control over how your posts are structured.\n\nIf you're curious how it works, I've put together a quick guide %2\$s.\n\nAnd if you're enjoying the plugin, taking a moment to %3\$s really helps.\n\n%4\$s",
					'post-to-google-my-business'
				),
				esc_html($current_user->display_name),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://docs.digitaldistortion.dev/article/41-dynamic-templates-for-google-business-posts',
					esc_html__('here', 'post-to-google-my-business')
				),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://wordpress.org/plugins/post-to-google-my-business/#reviews',
					esc_html__('leave a rating', 'post-to-google-my-business')
				),
				sprintf(
					'<strong>%s</strong><br /><i>%s</i>',
					'Koen',
					esc_html__('Plugin Developer', 'post-to-google-my-business')
				)
			)),
			'img/koen.png',
			esc_html__('Developer profile photo', 'post-to-google-my-business')
		);

		$this->notification_manager->add_notification($notification);
	}
}