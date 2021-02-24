<?php

namespace TgUtils\Templating;

use TgI18n\I18N;

/** Provides some basic templating mechanism */
class Processor {

	protected $objects;
	protected $snippets;
	protected $formatters;
	public    $language;

	/**
	 * @param array $objects    - objects with key => object items
	 * @param array $snippets  - snippets with key => snippet object items
	 * @param array $formatters - formatters with key => formatter object items
	 */
	public function __construct($objects = NULL, $snippets = NULL, $formatters = NULL, $language = NULL) {
		$this->objects    = $objects    != NULL ? $objects    : array();
		$this->snippets   = $snippets   != NULL ? $snippets   : array();
		$this->formatters = $formatters != NULL ? $formatters : array();
		$this->language   = $language   != NULL ? $language   : \TgI18n\I18N::$defaultLangCode;
	}

	/**
	 * Sets the language.
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
      * Replace any variables in content with values.
	  * a variable is a text with {{object.attribute}}
	  * if attribute is missing, a snippet with this name will be searched
	  * @param string $s    - the content to be processed
	  */
	public function process($s) {
		$rc = '';
		$matches = array();
		preg_match_all('/{{(.*?)}}/', $s, $matches, PREG_OFFSET_CAPTURE);
		$fullMatches = $matches[0];
		$slimMatches = $matches[1];
		$lastEnd = 0;
		for ($i=0; $i<count($fullMatches); $i++) {
			$fMatch = $fullMatches[$i];
			$sMatch = $slimMatches[$i]; 
			$newStart = $fMatch[1];
			$length   = strlen($fMatch[0]);
			// Take over the plain text since last match
			if ($newStart > $lastEnd) $rc .= substr($s, $lastEnd, $newStart-$lastEnd);
			// Take over the replacement
			$rc .= $this->getVar($sMatch[0]);
			// Prepare next iteration
            $lastEnd = $newStart + $length;
		}
		// Finally take the rest
		if ($lastEnd < strlen($s)) $rc .= substr($s, $lastEnd);
		return $rc;
	}

	/** Returns the variable with given content definition.
	  * A variable is a text with object.attribute
	  * If attribute is missing, a snippet with this name will be searched
	  */
	protected function getVar($s) {
		$parts     = explode('.', $s);
		$objectKey = $parts[0];
		if (count($parts) > 1) return $this->getAttribute($objectKey, $parts[1]);
		else {
			// Is there a string object?
			$object = $this->getObject($objectKey);
			if (is_string($object)) return $object;

			// Try a snippet
			$parts      = explode(':', $objectKey);
			$snippetKey = array_shift($parts);
			$snippet    = $this->getSnippet($snippetKey);
			if ($snippet != NULL) {
				if (is_string($snippet)) return $snippet;
				if (is_array($snippet))  return I18N::_($snippet, $this->language);
				return $snippet->getOutput($this, $parts);
			}
		}
		return '[Not defined: '.$s.']';
	}

	/**
	 * Returns the object with the given key or NULL.
	 */
	public function getObject($name) {
		return isset($this->objects[$name]) ? $this->objects[$name] : NULL; 
	}

	/**
	 * Returns the formatter with the given key or NULL.
	 */
	public function getFormatter($name) {
		return isset($this->formatters[$name]) ? $this->formatters[$name] : NULL; 
	}

	/**
	 * Returns the snippet with the given key or NULL.
	 */
	public function getSnippet($name) {
		return isset($this->snippets[$name]) ? $this->snippets[$name] : NULL; 
	}

    /** 
	  * Returns the value of the attribute in the object.
	  * An attribute can have a formatter definition attached e.g. created_on:datetime
	  * The formatter "datetime" will be used then.
	  * More arguments for the formatter can follow, separated with : again
	  */
	public function getAttribute($objName, $attr) {
		$object = $this->getObject($objName);
		$rc = '';
		if ($object != null) {
			// Split attributeName from format instructions
			$attrDef    = explode(':', $attr);
			$attrName   = array_shift($attrDef);
			$attrFormat = count($attrDef) > 0 ? array_shift($attrDef) : 'plain';
			if (isset($object->$attrName)) {
				$value = $object->$attrName;
				$rc    = '';
				// check formatting
				if ($attrFormat != 'plain') {
					$formatter = $this->getFormatter($attrFormat);
					if ($formatter != NULL) {
						$rc = $formatter->format($value, $attrDef, $this);
					}
				} else if (is_object($value)) {
					if (is_a($value, 'TgUtils\\Date')) {
						$formatter = $this->getFormatter('date');
						if ($formatter == NULL) $formatter = new DateFormatter();
						$rc = $formatter->format($value, $attrDef, $this);
					} else {
						$rc = $value->__toString();
					}
				} else {
					$rc = $value;
				}
			}
		}
		return $rc;
	}

}
