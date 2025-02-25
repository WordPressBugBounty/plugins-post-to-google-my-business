<?php

namespace PGMB\Notices;

use PGMB\Vendor\TypistTech\WPAdminNotices\StickyNotice;

class BrandedStickyNotice extends StickyNotice {
	public function __construct($handle, $message, $link = null, $type = null) {
		$title = esc_html__('Post to Google My Business', 'post-to-google-my-business');

		$content = sprintf(
			'<p><strong>%s</strong><br /><br />%s%s</p>',
			$title,
			$message,
			$link ? '<br /><br />' . $link : ''
		);

		parent::__construct($handle, $content, $type);
	}

}