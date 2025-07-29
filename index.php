<?php
// Start session
session_start();

// Require libraries from folder libraries
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/helpers.php';

// Autoload Core Libraries
require_once 'app/controllers/Controller.php';
spl_autoload_register(function ($className) {
    if (file_exists('app/models/' . $className . '.php')) {
        require_once 'app/models/' . $className . '.php';
    }
});

// Basic routing
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

$controllerFile = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
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