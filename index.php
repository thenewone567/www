<?php

// Start the session
require_once 'helpers/Session.php';
Session::start();

// Define the root path
define('ROOT_PATH', __DIR__ . '/');

// Autoload all the classes
spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    if (file_exists(ROOT_PATH . 'controllers/' . $className . '.php')) {
        require_once ROOT_PATH . 'controllers/' . $className . '.php';
    } elseif (file_exists(ROOT_PATH . 'models/' . $className . '.php')) {
        require_once ROOT_PATH . 'models/' . $className . '.php';
    }
});

// TCPDF Autoloader
require_once(ROOT_PATH . 'vendor/tcpdf/tcpdf.php');

// PHP QR Code Autoloader
require_once(ROOT_PATH . 'vendor/phpqrcode/qrlib.php');

// Get the requested URI from the `url` parameter
$requestUri = isset($_GET['url']) ? $_GET['url'] : '';

// Route the request
require_once ROOT_PATH . 'routes/web.php';

// If no route is matched, show a 404 error
http_response_code(404);
echo "404 Not Found";
