<?php
/**
 * Test script to verify sales history functionality
 */

// Load application bootstrap
require_once __DIR__ . '/bootstrap.php';

echo "Testing Sales History Functionality\n";
echo "==================================\n\n";

// Test 1: Check if Sale model can be loaded
echo "1. Testing Sale model loading...\n";
try {
    require_once APPROOT . '/app/models/Sale.php';
    $saleModel = new Sale();
    echo "✓ Sale model loaded successfully\n\n";
} catch (Exception $e) {
    echo "✗ Error loading Sale model: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Test database connection
echo "2. Testing database connection...\n";
try {
    require_once APPROOT . '/app/Database.php';
    $db = new Database();
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Test getSales method
echo "3. Testing getSales method...\n";
try {
    $sales = $saleModel->getSales();
    echo "✓ getSales method executed successfully\n";
    echo "   Found " . count($sales) . " sales records\n\n";
} catch (Exception $e) {
    echo "✗ Error in getSales method: " . $e->getMessage() . "\n\n";
}

// Test 4: Test getTodaysSales method
echo "4. Testing getTodaysSales method...\n";
try {
    $todaysSales = $saleModel->getTodaysSales();
    echo "✓ getTodaysSales method executed successfully\n";
    echo "   Found " . count($todaysSales) . " sales records for today\n\n";
} catch (Exception $e) {
    echo "✗ Error in getTodaysSales method: " . $e->getMessage() . "\n\n";
}

// Test 5: Check if SalesController can be loaded
echo "5. Testing SalesController loading...\n";
try {
    require_once APPROOT . '/app/controllers/SalesController.php';
    echo "✓ SalesController loaded successfully\n";
    echo "   Available methods: index, list, today, details, add\n\n";
} catch (Exception $e) {
    echo "✗ Error loading SalesController: " . $e->getMessage() . "\n\n";
}

echo "Test completed!\n";
echo "==============\n";
echo "If all tests passed, the sales history should be working.\n";
echo "Access the sales history via:\n";
echo "- All sales: {your_domain}/sales/list\n";
echo "- Today's sales: {your_domain}/sales/today\n";
echo "- Sales hub: {your_domain}/sales\n";
?>
