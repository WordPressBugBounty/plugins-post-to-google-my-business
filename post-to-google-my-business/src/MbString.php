<?php

namespace PGMB;

/**
 * Fallback class for when mbstring is not available
 */

class MbString {
	public static function strimwidth(string $string, int $start, int $width, string $trim_marker = '', string $encoding = null): string {
		if(function_exists('mb_strimwidth')){
			return \mb_strimwidth($string, $start, $width, $trim_marker, 'UTF-8');
		}

		return strlen($string) > $width ? substr($string, $start, ($width - strlen($trim_marker))).$trim_marker : $string;
	}

	public static function strwidth(string $string, string $encoding = null){
		if(function_exists('mb_strwidth')){
			return \mb_strwidth($string);
		}

		return strlen($string);
	}
}