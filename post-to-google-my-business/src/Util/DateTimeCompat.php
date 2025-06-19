<?php

namespace PGMB\Util;

use DateTime;
use DateTimeZone;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeImmutable;
use PGMB\Vendor\Rarst\WordPress\DateTime\WpDateTimeZone;

/**
 * Compatibility layer class to support pre wp 5.3
 */
class DateTimeCompat{

	/**
	 * @return DateTimeZone|WpDateTimeZone
	 */
	public static function get_timezone(){
		if(function_exists('wp_timezone')) {
			return wp_timezone();
		}
		return WpDateTimeZone::getWpTimezone();
	}

	public static function format_date(DateTime $date){
		if(function_exists('wp_date')){
			return wp_date( get_option( 'date_format' ), $date->getTimestamp(), $date->getTimezone() );
		}

		$legacy_datetime =  WpDateTimeImmutable::createFromMutable($date);

		return $legacy_datetime->formatDate();
	}
	public static function format_time(DateTime $date){
		if(function_exists('wp_date')){
			return wp_date( get_option( 'time_format' ), $date->getTimestamp(), $date->getTimezone() );
		}

		$legacy_datetime =  WpDateTimeImmutable::createFromMutable($date);

		return $legacy_datetime->formatTime();
	}

}