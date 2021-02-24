# php-i18n
Provides localization features for PHP apps. It reads a local i18n file for your application
and provides localized values for keys.

# License
This project is licensed under [GNU LGPL 3.0](LICENSE.md). 

# Installation

## By Composer

```
composer install technicalguru/i18n
```

## By Package Download
You can download the source code packages from [GitHub Release Page](https://github.com/technicalguru/php-i18n/releases)

# How to use

## Create a localization file
Create a file `i18n.php` somewhere where it can be accessed from your application.

```
<?php

/** Put here your language translations */
return array(
	'welcome' => array(
		'de' => 'Willkommen!',
		'en' => 'Welcome!',
	),
);
```

## Initialize I18N

Tell `I18N` where it will find your localizations and what your default language is.

```
use TgI18n\I18n;

I18N::$i18nFile        = '/my/path/to/i18n.php';
I18N::$defaultLangCode = 'de';
```

## Default Initialization

You don't need to tell `I18N` where to find your localizations when they exists in one
of these places. The file needs to be named `i18n.php`.

* Web Context Document Root as defined by `$_SERVER['CONTEXT_DOCUMENT_ROOT']`
* Web Document Root as defined by `$_SERVER['DOCUMENT_ROOT']`
* Current directory of your running script file (that is the one being executed)

You don't need to initialize the default language code, when it is `en`.

## Translating Values

Now you are setup to use your localizations:

```
use TgI18n\I18n;

echo I18N::_('welcome');
echo I18N::_('welcome', 'en');
echo I18N::_('welcome', 'de');
```

The `_($key)` static method always returns a value. If the given key cannot be found then the
key itself will be returned. 

If you prefer to have `NULL` returned, then use `__($key)`:

```
use TgI18n\I18n;

echo I18N::__('welcome');
echo I18N::__('welcome', 'en');
echo I18N::__('welcome', 'de');
```

# Contribution
Report a bug, request an enhancement or pull request at the [GitHub Issue Tracker](https://github.com/technicalguru/php-i18n/issues).

