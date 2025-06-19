<?php

namespace PGMB\Util;

class UTF16CodeUnitsUtil extends MbString{
	public static function strimwidth(string $string, int $start, int $width, string $trim_marker = '', ?string $encoding = null): string {
		if(!function_exists('iconv')){
			return parent::strimwidth($string, $start, $width, $trim_marker, $encoding);
		}

		$utf16_str = iconv('utf-8', 'utf-16le', $string);
		$utf16_length = strlen($utf16_str) / 2;

		if ($utf16_length <= $width) {
			return $string;
		}

		// Subtract the trim marker's character count from the width
		$width -= strlen(iconv('utf-8', 'utf-16le', $trim_marker)) / 2;

		$utf16_str = substr($utf16_str, $start * 2, $width * 2);
		$trimmed_str = @iconv('utf-16le', 'utf-8', $utf16_str);

		// In case the last character got cut in half, remove it
		if ($trimmed_str === false) {
			$utf16_str = substr($utf16_str, 0, -2);
			$trimmed_str = iconv('utf-16le', 'utf-8', $utf16_str);
		}

		return $trimmed_str . $trim_marker;

	}

	public static function strwidth(string $string, ?string $encoding = null ){
		if(!function_exists('iconv')){
			return parent::strwidth($string, $encoding);
		}
		return strlen(iconv('utf-8', 'utf-16le', $string)) / 2;
	}
}