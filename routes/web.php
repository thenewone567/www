<?php

// Routes
$routes = [
    '' => 'HomeController@index',
    'dashboard' => 'DashboardController@index',
    'login' => 'LoginController@showLoginForm',
    'login/process' => 'LoginController@login',
    'logout' => 'LoginController@logout',
    'sales/new' => 'SalesController@showNewSaleForm',
    'sales/create' => 'SalesController@createSale',
    'sales/history' => 'SalesController@showSalesHistory',
];

// Match the route
$method = $_SERVER['REQUEST_METHOD'];
foreach ($routes as $route => $action) {
    if ($requestUri === $route) {
        if ($method === 'POST' && ($route === 'login/process' || $route === 'sales/create')) {
            $parts = explode('@', $action);
            $controllerName = $parts[0];
            $methodName = $parts[1];

            $controller = new $controllerName();
            $controller->$methodName();
            exit;
        } elseif ($method === 'GET') {
            $parts = explode('@', $action);
            $controllerName = $parts[0];
            $methodName = $parts[1];

            $controller = new $controllerName();
            $controller->$methodName();
            exit;
        }
    }
}
