<?php

namespace TgUtils;

/**
 * A helper class to render HTML tags easily.
 */
class Html {

    /**
     * Returns the opening tag with the given attributes.
     * @param string $tagName - the name of the tag
     * @param array $attributes - the attributes to be rendered (values will be encoded accordingly)
     * @return string the opening HTML tag rendered
     */
	public static function renderStartTag($tagName, $attributes = array()) {
		$rc = '<'.$tagName;
		foreach ($attributes AS $name => $value) {
			if (is_array($value)) $value = implode(' ', $value);
			if (is_string($value) && trim($value) != '') {
				$rc .= ' '.$name.'="'.htmlspecialchars($value).'"';
			}
		}

		$rc .= '>';
		return $rc;
	}

	/**
	 * Renders the closing tag.
     * @param string $tagName - the name of the tag
     * @return string the closing HTML tag rendered
     */
	public static function renderEndTag($tagName) {
		return '</'.$tagName. '>';
	}
}