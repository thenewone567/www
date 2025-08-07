<?php
/**
 * Application Configuration
 */

define("APP_NAME", "Hardware Store Management System");
define("APP_VERSION", "2.0.0");
define("APP_ENV", "development"); // development, production

// Paths
define("APPROOT", dirname(__DIR__));
define("DS", DIRECTORY_SEPARATOR);
define("URLROOT", "http://localhost");
define("SITENAME", "Home Hardware");

// Session configuration
ini_set("session.cookie_httponly", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.cookie_secure", 0); // Set to 1 for HTTPS

// Error reporting
switch (APP_ENV) {
    case "development":
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set("log_errors", 1);
        break;
    case "production":
    default:
        error_reporting(0);
        ini_set("display_errors", 0);
        break;
}

// Logging
define("LOG_FILE", APPROOT . "/storage/logs/app.log");
