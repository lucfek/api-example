<?php

namespace TgUtils\Templating;

/**
 * This formatter takes the format as a parameter and assumes
 * the value to any form of Date or parseable date.
 */
class DateFormatter implements Formatter {

	public function __construct($timezone = 'UTC') {
		$this->timezone = $timezone;
	}

	public function format($value, $params, Processor $processor) {
		if ($value == NULL) return '';
		if (!is_object($value) && !is_a($value, 'TgUtils\\Date')) {
			$value = new \TgUtils\Date($value, $this->timezone);
		}
		if (count($params) > 0) {
			switch ($params[0]) {
			case 'unix': return $value->toUnix();
			case 'iso8601': return $value->toISO8601(TRUE);
			case 'rfc822': return $value->toRFC822(TRUE);
			}
			return $value->format(\TgI18n\I18N::_($params[0]), TRUE, TRUE, $processor->language);
		}
		return $value->__toString();
	}
}
