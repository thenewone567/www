<?php
  // Start session
  session_start();

  // Require libraries from folder libraries
  require_once 'app' . DS . 'config.php';
  require_once 'app' . DS . 'Database.php';
  require_once 'app' . DS . 'helpers.php';

  // Autoload Core Libraries
  require_once 'app' . DS . 'controllers' . DS . 'Controller.php';
  spl_autoload_register(function($className){
      if (file_exists('app' . DS . 'models' . DS . $className . '.php')) {
          require_once 'app' . DS . 'models' . DS . $className . '.php';
      }
  });

  // Basic routing
  $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
  $url = filter_var($url, FILTER_SANITIZE_URL);
  $urlParts = explode('/', $url);

// Determine the controller and method
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

// Check if the controller file exists
$controllerFile = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;

    // Check if the class and method exist
    if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
        $controller = new $controllerName();
        call_user_func_array([$controller, $methodName], $params);
    } else {
        // Handle 404 - class or method not found
        echo "404 - Page Not Found (Invalid method)";
    }
} else {
    // Handle 404 - controller not found
    echo "404 - Page Not Found (Invalid controller)<br>";
    echo "Attempted to load controller at: " . $controllerFile;
}
?>
