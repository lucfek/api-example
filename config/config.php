<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/vendor/autoload.php';

// DATABASE SETTINGS
define('DB_HOST', 'localhost');
define('DB_NAME', 'api');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// ONE-TIME PASSWORD SETTINGS
define('OTP_VALID', 5);
define('OTP_TRIES', 3);
define('OTP_LEN', 5);

// MAIL SETTINGS
define('MAIL_USER', "dishroller@gmail.com");
define('MAIL_PASSWORD', 'bcbcbabc123');
define('MAIL_HOST', "smtp.gmail.com");
define('MAIL_AUTH', TRUE);
define('MAIL_PORT', 587);
define('MAIL_SECURE', "TLS");

// JWT
define('JWT_EXP_TIME', 60*60);
define('JWT_KEY', 'example_key');