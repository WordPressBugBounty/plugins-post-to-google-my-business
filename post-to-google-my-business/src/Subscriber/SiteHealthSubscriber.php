<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;

class SiteHealthSubscriber implements SubscriberInterface {

	public static function get_subscribed_hooks() {
		return [
			'site_status_tests'     => 'add_mbstring_test',
		];
	}

	public function add_mbstring_test(array $tests): array{
		$tests['direct']['pgmb_mbstring_test'] = [
				'label' => __('Post to Google My Business mbstring test', 'post-to-google-my-business'),
				'test' => [$this, 'run_test'],
				'skip_cron' => true,
			];
		return $tests;
	}

	public function run_test() {
		$mbstring_enabled = extension_loaded('mbstring') && function_exists('mb_strimwidth');
		$status = $mbstring_enabled ? 'good' : 'critical';
		$label = $mbstring_enabled ? __('Post to Google My Business: mbstring is enabled', 'post-to-google-my-business') : __('Post to Google My Business: mbstring is not enabled', 'post-to-google-my-business');
		$description = sprintf('<p>%s</p>', $mbstring_enabled ? __('The PHP mbstring extension helps cut your GBP posts to the appropriate max length', 'post-to-google-my-business') : __('The PHP mbstring extension is not enabled on your server. It is required for cutting your posts to the appropriate max length. Enable it through your webhosting control panel or ask your server administrator to enable it for you.', 'post-to-google-my-business'));

		return [
			'label' => $label,
			'status' => $status,
			'badge' => [
				'label' => __('Post to Google My Business', 'post-to-google-my-business'),
				'color' => 'blue',
			],
			'description' => $description,
			'actions' => '',
			'test' => 'pgmb_mbstring_test'
		];
	}
}