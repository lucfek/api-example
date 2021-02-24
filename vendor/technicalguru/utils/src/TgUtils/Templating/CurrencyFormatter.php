<?php

namespace TgUtils\Templating;

/**
 * This formatter takes the currency as a parameter.
 */
class CurrencyFormatter implements Formatter {

	public function __construct() {
	}

	public function format($value, $params, Processor $processor) {
		if ($value == NULL) return '';
		$currency = count($params) > 0 ? $params[0] : '';
		return trim(\TgUtils\FormatUtils::formatPrice($value, $currency, $processor->language, ' '));
	}
}
