<?php

namespace PGMB\Upgrader;

use PGMB\Admin\DashboardPage;
use PGMB\Notifications\BasicNotification;
use PGMB\Notifications\NotificationManager;

class Upgrade_3_1_6 implements Upgrade {

	/**
	 * @var NotificationManager
	 */
	private $notification_manager;

	public function __construct(NotificationManager $notification_manager) {
		$this->notification_manager = $notification_manager;
	}

	public function run() {
		if (!class_exists( 'woocommerce' ) ) { return; }

		$current_user = wp_get_current_user();
		$notification = BasicNotification::create(
			DashboardPage::NOTIFICATION_SECTION,
			'3_1_6_psfg_promotional',
			esc_html__('Easily sync your WooCommerce products to GBP', 'post-to-google-my-business'),
			nl2br(sprintf(
				/* translators: %1$s is username/display name, %2$s is link to Product Sync for GBP plugin, %3$s is link to early access, %4$s is developer name (Koen) with "Plugin Developer" on the next line */
				esc_html__("Hey %1\$s,\n\nI noticed you're using WooCommerce and wanted to tell you about the new plugin I've been working on: %2\$s.\n\nIt makes it super easy to sync your entire WooCommerce product catalog directly to your Google Business Profile listing.\n\nIf you're interested in giving it a try, then %3\$s.\n\n%4\$s", 'post-to-google-my-business'),
				esc_html($current_user->display_name),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://tycoonmedia.net/product-sync-for-gbp/?utm_source=wordpress&utm_medium=plugin&utm_campaign=psfg+prelaunch&utm_content=plugin+name',
					esc_html__('Product Sync for GBP', 'post-to-google-my-business')
				),
				sprintf(
					'<a target="_blank" href="%s">%s</a>',
					'https://tycoonmedia.net/product-sync-for-gbp/?utm_source=wordpress&utm_medium=plugin&utm_campaign=psfg+prelaunch&utm_content=early+access+link',
					esc_html__('click here to get early access', 'post-to-google-my-business')
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
}