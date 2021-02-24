<?php

namespace TgUtils;

class JSON {

	/**
	 * Encodes a value after deep inspection for any values to be transformed.
	 * These transformations are made:
	 * \TgUtils\Date            - transformed to ISO8601 string
	 * \TgUtils\SelfJsonEncoder - transformed to according structure (using json_decode($mixed->json_encode))
	 * any other object         - deep inspected with get_object_vars() and transformed to \stdClass
	 * array                    - deep inspected and transformed to arrays respecting keys
	 * any other value          - no transformation
	 *
	 * @param mixed $mixed - the value to be transformed
	 * @param int   $flags - see PHP json_encode() documentation
	 * @param int   $depth - see PHP json_encode() documentation
	 * @return string - JSON representation of $mixed
	 */
	public static function encode($mixed, int $flags=0, int $depth=512) {
		return json_encode(self::transformForEncode($mixed), $flags, $depth);
	}

	/**
	 * Transforms a value recursively in order to detect dates and self-encoders.
	 * These transformations are made:
	 * \TgUtils\Date            - transformed to ISO8601 string
	 * \TgUtils\SelfJsonEncoder - transformed to according structure (using json_decode($mixed->json_encode))
	 * any other object         - deep inspected with get_object_vars() and transformed to \stdClass
	 * array                    - deep inspected and transformed to arrays respecting keys
	 * any other value          - no transformation
	 *
	 * @param  mixed $mixed - the value to be transformed
	 * @return mixed - the transformed value
	 */
	public static function transformForEncode($mixed) {
		if (is_object($mixed)) {
			if (is_a($mixed, 'TgUtils\\Date')) {
				$mixed = $mixed->toISO8601(TRUE);
			} else if (is_a($mixed, 'TgUtils\\SelfJsonEncoder')) {
				$mixed = json_decode($mixed->json_encode());
			} else {
				$rc = new \stdClass;
				foreach (get_object_vars($mixed) AS $name => $value) {
					$rc->$name = self::transformForEncode($value);
				}
				$mixed = $rc;
			}
		} else if (is_array($mixed)) {
			$rc = array();
			foreach ($mixed AS $key => $value) {
				$rc[$key] = self::transformForEncode($value);
			}
			$mixed = $rc;
		}
		return $mixed;
	}

}
