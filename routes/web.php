<?php

// Routes
$routes = [
    '' => 'HomeController@index',
];

// Match the route
foreach ($routes as $route => $action) {
    if ($requestUri === $route) {
        $parts = explode('@', $action);
        $controllerName = $parts[0];
        $methodName = $parts[1];

        $controller = new $controllerName();
        $controller->$methodName();
        exit;
    }
}
