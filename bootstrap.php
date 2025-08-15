<?php
/**
 * Application Bootstrap
 * Improved autoloading and initialization
 */

// Start output buffering early to prevent header issues
ob_start();

// Load configurations
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/config/database.php";

// Composer autoloader
if (file_exists(__DIR__ . "/vendor/autoload.php")) {
    require_once __DIR__ . "/vendor/autoload.php";
}

// Custom autoloader for app classes
spl_autoload_register(function ($className) {
    $directories = [
        __DIR__ . "/app/controllers/",
        __DIR__ . "/app/models/",
        __DIR__ . "/app/",
    ];

    foreach ($directories as $directory) {
        $file = $directory . $className . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Helper functions
require_once __DIR__ . "/app/helpers.php";
require_once __DIR__ . "/app/helpers/permissions.php";
require_once __DIR__ . "/app/helpers/transaction_verification.php";

// Application timezone (set before any date() usage). Adjust if deployment region differs.
date_default_timezone_set('Asia/Kolkata'); // Ensures dates like Aug 10 appear correctly for IST

// Provide a global helper for current date (Y-m-d) respecting timezone
if (!function_exists('app_current_date')) {
    function app_current_date()
    {
        return date('Y-m-d');
    }
}

// Load Session Manager
require_once __DIR__ . "/app/SessionManager.php";

// Initialize secure session management
SessionManager::init();

// Error logging function
function logError($message, $context = [])
{
    $timestamp = date("Y-m-d H:i:s");
    $contextStr = !empty($context) ? " Context: " . json_encode($context) : "";
    $logMessage = "[$timestamp] ERROR: $message$contextStr" . PHP_EOL;

    if (defined("LOG_FILE")) {
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Set error handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    logError("$message in $file on line $line", [
        "severity" => $severity,
        "file" => $file,
        "line" => $line
    ]);

    if (APP_ENV === "development") {
        echo "<div style=\"background: #fee; border: 1px solid #fcc; padding: 10px; margin: 5px;\">";
        echo "<strong>Error:</strong> $message<br>";
        echo "<strong>File:</strong> $file<br>";
        echo "<strong>Line:</strong> $line<br>";
        echo "</div>";
    }

    return true;
});

// Set exception handler  
set_exception_handler(function ($exception) {
    logError($exception->getMessage(), [
        "file" => $exception->getFile(),
        "line" => $exception->getLine(),
        "trace" => $exception->getTraceAsString()
    ]);

    if (APP_ENV === "development") {
        echo "<div style=\"background: #fee; border: 1px solid #fcc; padding: 10px; margin: 5px;\">";
        echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<details><summary>Stack Trace</summary><pre>" . $exception->getTraceAsString() . "</pre></details>";
        echo "</div>";
    } else {
        echo "<h2>Application Error</h2><p>An error occurred. Please contact support.</p>";
    }
});
