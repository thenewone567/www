<?php
// Test Sales History Functionality
require_once __DIR__ . '/bootstrap.php';

echo "Testing Sales History Functionality\n";
echo "==================================\n";

try {
    // Test Sale model loading
    echo "1. Testing Sale model loading...\n";
    $saleModel = new Sale();
    echo "✓ Sale model loaded successfully\n";

    // Test database connection
    echo "2. Testing database connection...\n";
    $db = new Database();
    echo "✓ Database connection successful\n";

    // Test getSales method
    echo "3. Testing getSales method...\n";
    $sales = $saleModel->getSales();
    echo "✓ getSales method executed successfully\n";
    echo "   Found " . count($sales) . " sales records\n";

    // Test getTodaysSales method
    echo "4. Testing getTodaysSales method...\n";
    $todaysSales = $saleModel->getTodaysSales();
    echo "✓ getTodaysSales method executed successfully\n";
    echo "   Found " . count($todaysSales) . " sales records for today\n";

    // Test SalesController loading
    echo "5. Testing SalesController loading...\n";
    $salesController = new SalesController();
    echo "✓ SalesController loaded successfully\n";

    // Get available methods
    $methods = get_class_methods('SalesController');
    $publicMethods = array_filter($methods, function ($method) {
        $reflection = new ReflectionMethod('SalesController', $method);
        return $reflection->isPublic() && $method !== '__construct';
    });
    echo "   Available methods: " . implode(', ', $publicMethods) . "\n";

    // Test view file existence
    echo "6. Testing view files existence...\n";
    $viewFiles = [
        'list' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'list.php',
        'today' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'today.php',
        'details' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'details.php',
        'header' => APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'
    ];

    foreach ($viewFiles as $name => $path) {
        if (file_exists($path)) {
            echo "✓ $name view file exists\n";
        } else {
            echo "✗ $name view file missing: $path\n";
        }
    }

    echo "\nTest completed!\n";
    echo "==============\n";
    echo "If all tests passed, the sales history should be working.\n";
    echo "Access the sales history via:\n";
    echo "- All sales: {your_domain}/sales/list\n";
    echo "- Today's sales: {your_domain}/sales/today\n";
    echo "- Sales hub: {your_domain}/sales\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>