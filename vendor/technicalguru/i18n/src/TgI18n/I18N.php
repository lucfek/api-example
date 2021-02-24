<?php

namespace TgI18n;

class I18N {

    /**
     * The default language code when not stated otherwise
     */
    public static $defaultLangCode = 'en';

    /**
     * The fallback language code when translation is not available in original language.
     */
    public static $fallbackLangCodes = array('en');

    /**
     * The main localization file.
     */
    public static $i18nFile = NULL;

    /**
     * The I18N language values
     */
    protected static $i18n = NULL;

    /**
     * Perform an sprintf with localized value of given key and parameters.
     *
     * @param string $key
     *            - the language key
     * @param string $langCode
     *            - the language code to be used for translation, default language when not given.
     * @param string $params
     *            - the params to the sprintf function
     * @return string the sprintf-ed value for the language 
     */
    public static function sprintf($key, $langCode, ...$params) {
		$value = self::_($key, $langCode);
		if ($value == NULL) return $value;
		array_unshift($params, $value);
		return call_user_func_array('sprintf', $params);
	}

    /**
     * Return the value with given key.
     *
     * @param string $key
     *            - the language key
     * @param string $langCode
     *            - the language code to be used for translation, default language when not given.
     * @return string the translated value for the language or the $key if not found
     */
    public static function _($key, $langCode = NULL) {
        if ($key == NULL) {
            return NULL;
        }
        $values = $key;
        if (is_string($values)) {
            $values = self::getValues($key);
        }
        if (is_object($values)) {
            $values = get_object_vars($values);
        }
        if ($langCode == NULL) {
            $langCode = self::$defaultLangCode;
        }
        if (is_string($values)) {
            return $values;
        }
        if (is_array($values)) {
            if (array_key_exists($langCode, $values)) {
                return $values[$langCode];
            } else {
				foreach (self::$fallbackLangCodes AS $fallback) {
					if (array_key_exists($fallback, $values)) {
		                return $values[$fallback];
					}
				}
			}
            return array_shift($values);
        }
        return $key;
    }

    /**
     * Returns all values for the given key.
     * <p>Three possible locations are searched for a i18n.php file:</p>
     * <ul>
     * <li>self::$i18nFile - set this variable before using localization</li>
     * <li>$_SERVER['CONTEXT_DOCUMENT_ROOT'].'/i18n.php' - usually the document root</li>
     * <li>$_SERVER['DOCUMENT_ROOT'].'/i18n.php' - file at document root</li>
     * <li>dirname($_SERVER['SCRIPT_FILENAME']).'/i18n.php' - file at current script directory</li>
     * </ul>
     *
     * @param string $key
     *            - key to be translated
     * @return array all translations as array(langCode =&gt; value)
     */
    public static function getValues($key) {
        if (self::$i18n == NULL) {
			self::doAutoload();
        }
        if (array_key_exists($key, self::$i18n)) {
            return self::$i18n[$key];
        }
        return $key;
    }

	/**
	 * Auto-loads all available localization files from documenet root and context document root
	 * and from script directory.
	 */
	protected static function doAutoload() {
		$files = array(
			self::$i18nFile
		);
		if (isset($_SERVER['CONTEXT_DOCUMENT_ROOT']) && $_SERVER['CONTEXT_DOCUMENT_ROOT']) {
			$files[] = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/i18n.php';
		}
		if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
			$files[] = $_SERVER['DOCUMENT_ROOT'] . '/i18n.php';
		}
		$files[] = dirname($_SERVER['SCRIPT_FILENAME']) . '/i18n.php';
		self::$i18n = array();
		foreach ($files as $file) {
			if (($file != NULL) && file_exists($file)) {
				self::$i18n = include($file);
				break;
			}
		}
	}

    /**
     * Merges in new localizations, eventually overriding existing ones.
     * @param string $file - the localization file to merge in
     * @param boolean $override - whether existing values shall be overriden (TRUE by default)
     */
    public static function addI18nFile($file, $override = TRUE) {
        if (($file != NULL) && file_exists($file)) {
            self::addValues(include($file), $override);
        }
    }
 
    /**
     * Merges in new localizations, eventually overriding existing ones.
     * @param array $values - the localization values to merge in
     * @param boolean $override - whether existing values shall be overriden (TRUE by default)
     */
    public static function addValues(array $values, $override = TRUE) {
        if ($values != NULL) {
            if (self::$i18n == NULL) {
                self::doAutoload();
			}
			foreach ($values AS $key => $i18n) {
				if (isset(self::$i18n[$key])) {
					foreach ($i18n AS $lang => $value) {
						if ($override || !isset(self::$i18n[$key][$lang])) {
							self::$i18n[$key][$lang] = $value;
						}
					}
				} else {
					self::$i18n[$key] = $i18n;
				}
			}
        }
    }
    
    /**
     * Return the value with given key.
     *
     * @param string $key
     *            - the language key
     * @param string $langCode
     *            - the language code to be used for translation, default language when not given.
     * @return string the translated value for the language or NULL if not found
     */
    public static function __($key, $langCode = NULL) {
        $rc = self::_($key, $langCode);
        if ($rc == $key) {
            $rc = NULL;
        }
        return $rc;
    }
    
    /**
     * <b>Danger!</b> This will reset all localization values.
     * <p>Use with care. Usually it makes no sense to use this function.</p>
     */
    public static function reset() {
        self::$defaultLangCode  = 'en';
        self::$fallbackLangCode = 'en';
        self::$i18nFile         = NULL;
        self::$i18n             = NULL;
    }
    
}
