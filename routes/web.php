<?php

// Routes
$routes = [
    '' => 'HomeController@index',
    'dashboard' => 'DashboardController@index',
    'login' => 'LoginController@showLoginForm',
    'login/process' => 'LoginController@login',
    'logout' => 'LoginController@logout',
    'register' => 'RegisterController@showRegistrationForm',
    'register/process' => 'RegisterController@register',
    'sales/new' => 'SalesController@showNewSaleForm',
    'sales/create' => 'SalesController@createSale',
    'sales/history' => 'SalesController@showSalesHistory',
    'purchases/new' => 'PurchasesController@showNewPurchaseForm',
    'purchases/create' => 'PurchasesController@createPurchase',
    'purchases/history' => 'PurchasesController@showPurchasesHistory',
    'products' => 'ProductsController@index',
    'products/add' => 'ProductsController@add',
    'products/edit' => 'ProductsController@edit',
    'products/delete' => 'ProductsController@delete',
    'inventory/receiving' => 'InventoryController@showReceivingForm',
    'inventory/receive' => 'InventoryController@receive',
    'inventory/restock' => 'InventoryController@showRestockForm',
    'inventory/restock/process' => 'InventoryController@restock',
    'inventory/putaway' => 'InventoryController@showPutawayForm',
    'inventory/putaway/process' => 'InventoryController@putaway',
    'inventory/cycle-count' => 'InventoryController@showCycleCountForm',
    'inventory/cycle-count/process' => 'InventoryController@cycleCount',
];

// Match the route
$method = $_SERVER['REQUEST_METHOD'];
foreach ($routes as $route => $action) {
    if ($requestUri === $route) {
        if ($method === 'POST') {
            if ($route === 'login/process' || $route === 'sales/create' || $route === 'purchases/create' || $route === 'register/process' || $route === 'products/add' || $route === 'products/edit' || $route === 'inventory/receive' || $route === 'inventory/restock' || $route === 'inventory/putaway' || $route === 'inventory/cycle-count') {
                $parts = explode('@', $action);
                $controllerName = $parts[0];
                $methodName = $parts[1];

                $controller = new $controllerName();
                $controller->$methodName();
                exit;
            }
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
