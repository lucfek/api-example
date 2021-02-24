# php-utils
This is a collection of useful classes and functions for every day PHP life. It includes things such as:

* Handling dates and times and various appearances of it
* Searching and sorting of arrays of objects
* Logging to error_log with different levels
* Notification to users across various requests within a session
* Extracting information from the current HTTP request
* Obfuscating sensitive information to protect data against spambots
* Slugifying strings for usage in URLs
* Generating random strings
* Formatting prices and units
* Simple text templating

These classes are no rocket science, just simple helpers that prevent from wiriting the
same code in various flavours over and over again.

# License
This project is licensed under [GNU LGPL 3.0](LICENSE.md). 

# Installation

## By Composer

```
composer install technicalguru/utils
```

## By Package Download
You can download the source code packages from [GitHub Release Page](https://github.com/technicalguru/php-utils/releases)

# Classes

## Request class
The Request class extracts many useful information from the current HTTP request. It provides:

* Header information
* GET and POST parameter values, assigning defaults if not present
* Getting the method, protocol, host and path information from the HTTP request
* Checking the elapsed time of this request

It is possible to create an object yourself, but it is recommend to use the singleton:

```
$request = \TgUtils\Request::getRequest();
```

Inspect the [source code](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Request.php) to find out about the various methods available.

## Date class

The date class handles issues for localization, timezones and conversion into different formats or creation from various formats. Here are some examples:

```
use TgUtils\Date;

$timezone = 'Europe/Berlin';

// Creating instances with local timezone
$date = new Date(time(), $timezone);
$date = Date::createLocalInstance(time(), $timezone);
$date = Date::createFromMysql($mySqlTimestampString, $timezone);

// The Epoch time in seconds
$unixTimestamp = $date->toUnix();

// Converting and formatting with local timezone 
$mysqlTimstamp = $date->toMysql(TRUE);
$iso8601       = $date->toISO8601(TRUE);
$someString    = $date->format('d.m.Y H:i:s', TRUE);

// Converting to UTC timezones instances
$mysqlTimstamp = $date->toMysql();
$iso8601       = $date->toISO8601();
$someString    = $date->format('d.m.Y H:i:s');
```

Inspect the [source code](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Date.php) to find out about the various methods available.

## Logging

Logging is very simple. Set your log level (if not INFO) and start logging:

```
use TgLog\Log;

// Set default settings before using the log:
Log::setDefaultLogLevel(Log::ERROR);
Log::setDefaultAppName('MyApplication');

// Or just on the singleton logging instance
Log::instance()->setLogLevel(Log::ERROR);
Log::instance()->setAppName('MyAppName');

// Simple line
Log::error('A simple error message');

// This message will not go into the log
Log::info('This message is lost.');

// Log an exception
Log::error('An expception was caught: ', $exception);

// Log an object for later debugging
Log::error('We have some problem here:', $object);
```

The Log can also help when you need to debug something:

```
use TgLog\Log;

// Get the stacktrace
$stacktrace = Log::getStackTrace(__FILE__);

// Or log it with INFO level
Log::infoStackTrace(__FILE__);
```

The `__FILE__` parameter will oppress the current file from appearing in the stacktrace. You
can easily use the function without this argument to have the current file included:

```
$stacktrace = Log::getStackTrace();
Log::infoStackTrace();
```

Finally, you can create your special instances of a log for some modules and log from there, e.g.

```
$moduleLog = new Log(Log::ERROR, 'MyAppModule');

$moduleLog->logInfo('Module started');
$moduleLog->logError('Exception occurred:', $exception);

```

## User Notifications

Sometimes it is hard to display success or error messages because the message needs to
be displayed in another script, another HTTP call or even just later. The following snippet
will remember the message across multiple call within a session.

```
use TgLog\Log;
use TgLog\Success;

Log::register(new Success('Your data was saved successfully.));
```

The registartion will not only remember the message but also print the message into
the error log when it's an instance of `Debug`, `Error`, `Warning` or `Info`.

In another call or script you can retrieve your messages again to display:

```
use TgLog\Log;
use TgLog\Success;

foreach (Log::get() AS $message) {
	switch ($message->getType()) {
	case 'success':
		echo '<div class="text-success">'.$message->getMessage().'</div>';
		break;
	case 'error':
		echo '<div class="text-danger">'.$message->getMessage().'</div>';
		break;
	}
}

// Finally, clean all messages
Log::clean();
```

## Authentication Helper
A simple authentication helper interface along with a default implementation is provided:

* [TgUtils\Auth\CredentialsProvider](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Auth/CredentialsProvider.php) - Interface to provide username and password to other objects
* [TgUtils\Auth\DefaultCredentialsProvider](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Auth/DefaultCredentialsProvider.php) - Simple default implementation of the interface

## Sensitive Data Obfuscation

Publishing sensitive data such as e-mail addresses and phone numbers is dangerous nowadays as spammers
grab such information automatically from websites. The utils package provides a javascript-based way
to obfuscate this information on websites. Its' idea is based on the rot13 obfuscation method but uses a
random character mapping instead of a fixed rotation. This idea was chosen because rot13 seems to be a kind of standard
in obfuscation and spammers might already be able to read them.

It shall be noted that it is still not impossible to read the information even when obfuscated. But it requires
a bit more sophisticated effort (Javascript execution) to gain the sensitive information.

**Idea:** The text to be obfuscated is replaced - char by char - by other characters from a map. This map is
generated uniquely for this special obfuscation instance. Other obfuscations on the same page will use different
maps. The HTML source displays only: `[javascript protected]`. However, a special javascript will run after
the page loaded and replace exactly this text with the real content.

Two obfuscation methods exists: a simple text obfuscation and an e-mail obfuscation which also creates a mailto: link
that the user can click.

Here is how you use it:

```
user \TgUtils\Obfuscation;

/*** Just create everything and put it in your HTML page **/
$htmlSource = Obfuscation::obfuscateText('+49 555 0123456');
$emailLink  = Obfuscation::obfuscateEmail('john.doe@example.com');

/*************************** OR ***************************/
// Use your own tag ID 
$id         = Obfuscation::generateObfuscationId();

// Use this ID to get the bot-resistent HTML source
$htmlSource = Obfuscation::getObfuscatedHtmlSpan($id);

// And get the javascript
$textJavascript   = Obfuscation::obfuscateText('+49 555 0123456', $id);
$emailJavascript  = Obfuscation::obfuscateEmail('john.doe@example.com', $id);

```

Please notice that not all characters are supported in the default character map. It covers mainly
e-mail addresses and phone numbers. However, you can pass your own character set to the obfuscate methods
as third argument. Please consult the [source code](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Obfuscation.php) for more details.

## Text Templating

To ease the generation of dynamic texts, a template processor is provided. This processor can work on texts that contain variables in 
curly brackets `{{variable-definition}}`. The processor knows objects, snippets and formatters.

**Objects** are application objects that hold attributes that you want to be replaced. An object's attribute will be referenced in a template
with `{{objectKey.attributeName}}`, e.g. `{{user.name}}`.

**Snippets** are more complex replacements that will be inserted in your template. This is useful when you need the same complex
text structure in multiple template generations, e.g. for a footer or a header text. Snippets are referenced in a template by
their keys only: `{{snippetKey}}`. A snippet is implemented by the interface [`Snippet`](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Templating/Snippet.php). A snippet can take parameters as `{{snippetKey:param1:param2...}}`.

**Formatters** can be used to format an object's attribute. Formatters can take parameters to further customize the formatting. A good example
is the [`DateFormatter`](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Templating/DateFormatter.php). The formatter
is referenced with the object's attribute by `{{objectKey.attribute:formatterKey:param1:param2...}}`, e.g. `{{user.created_on:date:rfc822}}`.

All three elements - objects, snippets and formatters - are given to the [`Processor`](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Templating/Processor.php) in its constructor:

```
$objects    = array('user'   => $myUser);
$snippets   = array('header' => new HeaderSnippet(), 'footer' => $new FooterSnippet());
$formatters = array('date'   => new DateFormatter());
$language   = 'en';
$processor = new Processor($objects, $snippets, $formatters, $language);
```

The language is for information and can be used in snippets or formatters to select the right text.

Finally you can process a template:

```
$template = '{{header}} Hello {{user.name}}! Your account was created on {{user.created_on:date:d/m/Y}}.{{footer}}';
echo $processor->process($template);

// Output is:
// IMPORTANT MESSAGE! Hello John Doe! Your account was created on 02/03/2017. Best regards!
```

## Other Utils
There are some daily tasks that need to be done in applications. The `Utils` class addresses a few of them:

```
use TgUtils\Utils;
use TgUtils\FormatUtils;
use TgUtils\Slugify;

// create a random string
$myId = Utils::generateRandomString(20);

// Find an object with UID 1
$myObject = Utils::findByUid($objectList, 3);

// Find an object with a certain name
$myObject = Utils::findBy($objectList, 'name', 'John');

// Sort an array of objects by its names
Utils::sort($objectList, 'name');

// Sort an array of objects in reverse order of names
Utils::sort($objectList, 'name', TRUE);

// Sort an array of objects in reverse order of names, ignore upper/lower case
Utils::sort($objectList, 'name', TRUE, TRUE);

// Get all names from the list of objects
$allNames = Utils::extractAttributeFromList($objectList, 'name');

// Mask a sensitive string
$masked = Utils::anonymize($aPhoneNumber);

// Slugify a string
$slug = Slugify::slugify('A text that turn into an URL');

// Format a price
$priceString = FormatUtils::formatPrice(3000.643, 'EUR');

// Format a file size
$fileSize = FormatUtils::formatUnit(3000643, 'B');
```

Inspect the [source code](https://github.com/technicalguru/php-utils/blob/main/src/TgUtils/Utils.php) to find more about the methods available.

# Contribution
Report a bug, request an enhancement or pull request at the [GitHub Issue Tracker](https://github.com/technicalguru/php-utils/issues).
