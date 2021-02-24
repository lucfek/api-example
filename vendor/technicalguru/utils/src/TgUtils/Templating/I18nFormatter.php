<?php

namespace TgUtils\Templating;

/**
 * This formatter assumes the value to be an array, an object or a i18n key
 * that I18N can process.
 */
class I18nFormatter implements Formatter {

	public function __construct() {
	}

	public function format($value, $params, Processor $processor) {
		if ($value == NULL) return '';
		return \TgI18n\I18n::_($value, $processor->language);
	}
}
