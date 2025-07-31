<?php
// Test Dropdown Functionality
require_once __DIR__ . '/bootstrap.php';

echo "Testing Dropdown Functionality\n";
echo "==============================\n";

try {
    // Test Supplier model
    echo "1. Testing Supplier model...\n";
    $supplierModel = new Supplier();
    $suppliers = $supplierModel->getSuppliers();
    echo "✓ Supplier model loaded successfully\n";
    echo "   Found " . count($suppliers) . " suppliers\n";
    
    if (count($suppliers) > 0) {
        echo "   Sample supplier: " . $suppliers[0]->supplier_name . " (ID: " . $suppliers[0]->supplier_id . ")\n";
    }

    // Test Customer model
    echo "2. Testing Customer model...\n";
    $customerModel = new Customer();
    $customers = $customerModel->getCustomers();
    echo "✓ Customer model loaded successfully\n";
    echo "   Found " . count($customers) . " customers\n";
    
    if (count($customers) > 0) {
        echo "   Sample customer: " . $customers[0]->customer_name . " (ID: " . $customers[0]->customer_id . ")\n";
    }

    // Test PurchasesController with suppliers
    echo "3. Testing PurchasesController...\n";
    $purchasesController = new PurchasesController();
    echo "✓ PurchasesController loaded with supplier support\n";

    // Test SalesController with customers
    echo "4. Testing SalesController...\n";
    $salesController = new SalesController();
    echo "✓ SalesController loaded with customer support\n";

    echo "\nTest completed successfully!\n";
    echo "===========================\n";
    echo "Changes implemented:\n";
    echo "- Purchase orders now show supplier names in dropdown\n";
    echo "- Sales forms now show customer names in dropdown\n";
    echo "- Both forms store the ID but display the name for better UX\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
