<?php
/**
 * Hardware Store Management System Entry Point
 * Updated to use new bootstrap system
 */

// Load application bootstrap
require_once __DIR__ . '/bootstrap.php';

// Basic routing
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Convert hyphenated URLs to CamelCase controller names
$controllerPart = !empty($urlParts[0]) ? $urlParts[0] : 'home';


// Special case: support /api/export/csv and /products/exportcsv as aliases for /products/exportCSV
if (strtolower($url) === 'api/export/csv' || strtolower($url) === 'products/exportcsv') {
    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/products/exportCSV');
    exit;
}

// Special case: handle /products/view/ID URLs by redirecting to /products/show/ID
if (preg_match('/^products\/view\/(\d+)$/', $url, $matches)) {
    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/products/show/' . $matches[1]);
    exit;
}

// Special case: handle /suppliers/view/ID URLs by redirecting to /suppliers/show/ID
if (preg_match('/^suppliers\/view\/(\d+)$/', $url, $matches)) {
    header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/suppliers/show/' . $matches[1]);
    exit;
}

// Handle specific URL mappings
if ($controllerPart === 'cycle-counts' || $controllerPart === 'cycle_counts') {
    // Redirect old cycle-counts routes to new inventory/cycle_counting route
    header('Location: ' . URLROOT . '/inventory/cycle_counting');
    exit();
} else {
    // Convert other hyphenated URLs to CamelCase
    $controllerName = str_replace('-', '', ucwords($controllerPart, '-')) . 'Controller';
}

$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

$controllerFile = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
error_log("Routing: URL='$url', Controller='$controllerName', Method='$methodName', File='$controllerFile'");
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            call_user_func_array([$controller, $methodName], $params);
        } else {
            // Handle 404 - method not found
            header("HTTP/1.0 404 Not Found");
            echo "404 - Page Not Found (Invalid method)";
        }
    } else {
        // Handle 404 - class not found
        header("HTTP/1.0 404 Not Found");
        echo "404 - Page Not Found (Invalid controller class)";
    }
} else {
    // Handle 404 - controller file not found
    header("HTTP/1.0 404 Not Found");
    echo "404 - Page Not Found (Invalid controller)<br>";
    echo "Attempted to load controller at: " . $controllerFile;
}
?>