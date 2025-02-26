<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 25-February-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace PGMB\Vendor\Rarst\WordPress\DateTime;

/**
 * Extension of DateTimeZone for WordPress.
 */
class WpDateTimeZone extends \DateTimeZone {

	/**
	 * Determine time zone from WordPress options and return as object.
	 *
	 * @return static
	 */
	public static function getWpTimezone() {

		$timezone_string = get_option( 'timezone_string' );

		if ( ! empty( $timezone_string ) ) {
			return new static( $timezone_string );
		}

		$offset  = get_option( 'gmt_offset' );
		$sign    = $offset < 0 ? '-' : '+';
		$hours   = (int) $offset;
		$minutes = abs( ( $offset - (int) $offset ) * 60 );
		$offset  = sprintf( '%s%02d:%02d', $sign, abs( $hours ), $minutes );

		return new static( $offset );
	}
}
