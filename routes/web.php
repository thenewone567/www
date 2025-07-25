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
    'purchases/rate-supplier' => 'PurchasesController@rateSupplier',
    'products' => 'ProductsController@index',
    'products/add' => 'ProductsController@add',
    'products/edit' => 'ProductsController@edit',
    'products/delete' => 'ProductsController@delete',
    'products/import-csv' => 'ProductsController@showCsvImportForm',
    'products/import-csv/process' => 'ProductsController@importCsv',
    'products/export-csv' => 'ProductsController@exportCsv',
    'products/qr-code' => 'ProductsController@generateQrCode',
    'inventory/receiving' => 'InventoryController@showReceivingForm',
    'inventory/receive' => 'InventoryController@receive',
    'inventory/restock' => 'InventoryController@showRestockForm',
    'inventory/restock/process' => 'InventoryController@restock',
    'inventory/putaway' => 'InventoryController@showPutawayForm',
    'inventory/putaway/process' => 'InventoryController@putaway',
    'inventory/cycle-count' => 'InventoryController@showCycleCountForm',
    'inventory/cycle-count/process' => 'InventoryController@cycleCount',
    'invoices/show/{id}' => 'InvoicesController@show',
    'invoices/pdf/{id}' => 'InvoicesController@generatePDF',
    'users' => 'UsersController@index',
    'users/add' => 'UsersController@add',
    'users/edit' => 'UsersController@edit',
    'users/delete' => 'UsersController@delete',
    'users/login-activity' => 'UsersController@showLoginActivity',
    'audit-log' => 'AuditLogController@index',
    'reports' => 'ReportsController@index',
    'reports/generate' => 'ReportsController@generate',
    'reports/export' => 'ReportsController@export',
];

// Match the route
$method = $_SERVER['REQUEST_METHOD'];
foreach ($routes as $route => $action) {
    $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
    if (preg_match("#^$pattern$#", $requestUri, $matches)) {
        if ($method === 'POST') {
            if ($route === 'login/process' || $route === 'sales/create' || $route === 'purchases/create' || $route === 'register/process' || $route === 'products/add' || $route === 'products/edit' || $route === 'inventory/receive' || $route === 'inventory/restock' || $route === 'inventory/putaway' || $route === 'inventory/cycle-count' || $route === 'users/add' || $route === 'users/edit' || $route === 'products/import-csv/process' || $route === 'purchases/rate-supplier') {
                $parts = explode('@', $action);
                $controllerName = $parts[0];
                $methodName = $parts[1];

                $controller = new $controllerName();
                $controller->$methodName();
                exit;
            }
        }
        array_shift($matches);
        $parts = explode('@', $action);
        $controllerName = $parts[0];
        $methodName = $parts[1];

        $controller = new $controllerName();
        call_user_func_array([$controller, $methodName], $matches);
        exit;
    }
}
