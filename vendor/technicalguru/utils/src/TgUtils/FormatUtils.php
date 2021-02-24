<?php

namespace TgUtils;

use \TgI18n\I18N;

I18N::addI18nFile(__DIR__ . '/../utils_i18n.php', FALSE);

/**
 * Provides some formatting utils.
 * @author ralph
 *        
 */
class FormatUtils {

	/**
	 * Format a price value using localization.
	 * @param float $value - the value
	 * @param string $currency - the currency
	 * @param string $language - the language code (optional, default language of I18N class).
	 * @param string $spaceChar - the space character to be used between value and currency (optional, default is HTML non-breaking space).
	 * @return string the formatted price in localized manner.
	 */
    public static function formatPrice($value, $currency, $language = null, $spaceChar = '&nbsp;') {
		return number_format(floatval($value), 2, I18N::_('decimal_point', $language), I18N::_('thousand_sep', $language)).$spaceChar.$currency;
	}
	
	/**
	 * Format a unit by using prefixes (decimal and computer bytes only).
	 * @param int $size - the size to be formatted
	 * @param string $unit - the unit string
	 * @param int $precision - how many digits after decimal point shall be displayed (optional, default is 1)
	 * @param string $language - the language to be used for number formatting separators (optional, default is NULL)
	 * @param bool $bytes - TRUE when computer byte counting is used, FALSE when decimal base shall be applied (optional, default is TRUE)
	 * @return string a formatted string
	 */
	public static function formatUnit($size, $unit, $precision = 1, $language = NULL, $bytes = TRUE) {
	    $prefixes = $bytes ? array('K', 'M', 'G', 'T' ) : array('k', 'M', 'G', 'T');  
	    $rc       = $unit;
	    $base     = $bytes ? 1024 : 1000;
		// Only until GB
		if ($size > $base) {
			$size = $size/$base;
			$rc   = $prefixes[0].$unit;
		}
		if ($size > $base) {
			$size = $size/$base;
			$rc   = $prefixes[1].$unit;
		}
		if ($size > $base) {
			$size = $size/$base;
			$rc   = $prefixes[2].$unit;
		}
		if ($size > $base) {
			$size = $size/$base;
			$rc   = $prefixes[3].$unit;
		}
		$size = $rc != $unit ? number_format($size, $precision, I18N::_('decimal_point', $language), I18N::_('thousand_sep', $language)) : number_format($size, 0, I18N::_('decimal_point', $language), I18N::_('thousand_sep', $language));
		return $size.' '.$rc;
	}

	/**
	 * Provides the complete Exception message as string with newlines.
	 * This method does not shorten any string as the getTraceAsString() method does.
	 * @param Throwable $throwable the exception to trace
	 * @return string the complete exception message and stack
	 */
	public static function getTraceAsString($throwable) {
		return implode("\n", self::getTraceLines($throwable));
	}

	/**
	 * Provides the complete Exception message as array of strings.
	 * This method does not shorten any string as the getTraceAsString() method does.
	 * @param Throwable $throwable the exception to trace
	 * @return array the complete exception message and stack in separate strings
	 */
	public static function getTraceLines($throwable) {
		$rc = array(get_class($throwable).': '.$throwable->getMessage());
		$rc[] = 'at '.$throwable->getFile().'(line '.$throwable->getLine().'): ';

		$trace = $throwable->getTrace();
		foreach ($trace AS $traceLine) {
			$rc[] = self::getTraceLine($traceLine);
		}
		$previous = $throwable->getPrevious();
		if ($previous != NULL) {
			$rc[] = 'Caused by:';
			$rc = array_merge($rc, self::getTraceLines($previous));
		}
		return $rc;
	}

	/**
	 * Provides the line of a stack trace as string (no shortening).
	 * @param array $entry - the entry of the stack trace as given by getTrace()
	 * @return string the entry as string
	 */
	protected static function getTraceLine($entry) {
		$rc = 'at ';
		if (isset($entry['file']))  $rc .= $entry['file'];
		if (isset($entry['line']))  $rc .= ' (line '.$entry['line'].')';
		if (isset($entry['class']) || isset($entry['type']) || isset($entry['function'])) {
			if (isset($entry['file']))  $rc .= ': ';
			if (isset($entry['class'])) $rc .= $entry['class'];
			if (isset($entry['type']))  $rc .= $entry['type'];
			if (isset($entry['function'])) {
				$rc .= $entry['function'].'(';
				if (isset($entry['args'])) {
					$first = TRUE;
					foreach ($entry['args'] AS $arg) {
						if ($first) $first = FALSE;
						else $rc .= ',';
						if (is_object($arg)) $rc .= get_class($arg);
						else if (is_array($arg)) $rc .= 'array';
						else if (is_string($arg)) $rc .= "'$arg'";
						else $rc .= $arg;
					}
				}
				$rc .= ')';
			}
		}
		return $rc;
	}

}

