<?php
require_once 'bootstrap.php';

// Test session setup
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';

echo "Testing /receiving/pending page functionality\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // 1. Test if ReceivingController exists and loads
    echo "1. Testing ReceivingController:\n";
    if (class_exists('ReceivingController')) {
        echo "   ✓ ReceivingController class exists\n";
        
        $controller = new ReceivingController();
        echo "   ✓ Controller instantiated successfully\n";
        
        if (method_exists($controller, 'pending')) {
            echo "   ✓ pending() method exists\n";
        } else {
            echo "   ✗ pending() method missing\n";
        }
    } else {
        echo "   ✗ ReceivingController class not found\n";
    }
    
    // 2. Test database connectivity and data
    echo "\n2. Testing database and data:\n";
    $purchaseModel = new Purchase();
    
    // Test basic query
    $allPurchases = $purchaseModel->getPurchases();
    echo "   Total purchases in database: " . count($allPurchases) . "\n";
    
    // Test pending query
    $pendingOrders = $purchaseModel->getPurchases([
        'status' => ['pending', 'sent', 'partially_received']
    ]);
    echo "   Pending purchases: " . count($pendingOrders) . "\n";
    
    if (!empty($pendingOrders)) {
        $first = $pendingOrders[0];
        echo "   Sample order data:\n";
        echo "     - ID: {$first->purchase_id}\n";
        echo "     - Status: {$first->status}\n";
        echo "     - Supplier: " . ($first->supplier_name ?? 'N/A') . "\n";
        echo "     - Purchase Number: " . ($first->purchase_number ?? 'N/A') . "\n";
    }
    
    // 3. Test supplier model
    echo "\n3. Testing suppliers:\n";
    $supplierModel = new Supplier();
    $suppliers = $supplierModel->getSuppliers();
    echo "   Total suppliers: " . count($suppliers) . "\n";
    
    // 4. Test view file existence
    echo "\n4. Testing view files:\n";
    $viewFile = __DIR__ . '/app/views/receiving/pending.php';
    if (file_exists($viewFile)) {
        echo "   ✓ pending.php view file exists\n";
        echo "   File size: " . filesize($viewFile) . " bytes\n";
    } else {
        echo "   ✗ pending.php view file missing\n";
    }
    
    // 5. Test header and footer files
    $headerFile = __DIR__ . '/app/views/layouts/header.php';
    $footerFile = __DIR__ . '/app/views/layouts/footer.php';
    
    echo "   Header file: " . (file_exists($headerFile) ? "✓ exists" : "✗ missing") . "\n";
    echo "   Footer file: " . (file_exists($footerFile) ? "✓ exists" : "✗ missing") . "\n";
    
    // 6. Test session and authentication
    echo "\n5. Testing session:\n";
    echo "   Session ID: " . session_id() . "\n";
    echo "   User logged in: " . (isLoggedIn() ? "✓ Yes" : "✗ No") . "\n";
    echo "   User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "\n";
    
    // 7. Test URL constants
    echo "\n6. Testing constants:\n";
    echo "   URLROOT: " . (defined('URLROOT') ? URLROOT : 'Not defined') . "\n";
    echo "   APPROOT: " . (defined('APPROOT') ? APPROOT : 'Not defined') . "\n";
    
    // 8. Test if we can simulate the actual controller call
    echo "\n7. Testing controller execution:\n";
    
    // Clear any GET parameters that might interfere
    $_GET = [];
    
    try {
        ob_start();
        $controller = new ReceivingController();
        $controller->pending();
        $output = ob_get_clean();
        
        echo "   ✓ Controller executed without errors\n";
        echo "   Output length: " . strlen($output) . " characters\n";
        
        // Check for key content
        if (strpos($output, 'Pending Receipts') !== false) {
            echo "   ✓ Contains 'Pending Receipts' title\n";
        } else {
            echo "   ✗ Missing 'Pending Receipts' title\n";
        }
        
        if (strpos($output, 'pending_orders') !== false && strpos($output, 'empty') === false) {
            echo "   ✓ Has pending orders data\n";
        } else {
            echo "   ✗ No pending orders data or shows empty\n";
        }
        
        if (strpos($output, 'theme-table') !== false) {
            echo "   ✓ Contains table markup\n";
        } else {
            echo "   ✗ Missing table markup\n";
        }
        
        // Save output to inspect
        file_put_contents('debug_pending_output.html', $output);
        echo "   Full output saved to debug_pending_output.html\n";
        
    } catch (Exception $e) {
        echo "   ✗ Controller execution failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
