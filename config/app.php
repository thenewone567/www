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
define("SITENAME", "Bhai Ji Traders");

// Session configuration (only if headers not sent)
if (!headers_sent()) {
    ini_set("session.cookie_httponly", 1);
    ini_set("session.use_only_cookies", 1);
    ini_set("session.cookie_secure", 0); // Set to 1 for HTTPS
}

// Error reporting
switch (APP_ENV) {
    case "development":
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ini_set("log_errors", 1);
        // Suppress warnings that might interfere with headers during page refresh
        if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
            ini_set("display_errors", 0);
        }
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
define("SESSION_LOG_FILE", APPROOT . "/storage/logs/session.log");

// Session Management Settings
define("SESSION_TIMEOUT", 1800); // 30 minutes in seconds
define("SESSION_REFRESH_INTERVAL", 300); // 5 minutes in seconds
define("VERIFY_USER_EXISTS", false); // Set to true for extra security (may impact performance)
