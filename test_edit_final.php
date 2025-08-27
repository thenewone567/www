<?php
require_once 'bootstrap.php';

echo "=== TESTING EDIT USER FUNCTIONALITY WITH SOURCE TABLES ===\n";

$userModel = new User();

// Test getUserByIdAndTable for each source
echo "\n1. Testing getUserByIdAndTable:\n";

// Test customer
echo "\nCustomer (ID: 6 from customers table):\n";
$customer = $userModel->getUserByIdAndTable(6, 'customers');
if ($customer) {
    echo "  ✓ Found: " . $customer->name . "\n";
    echo "  ✓ Email: " . ($customer->email ?? 'N/A') . "\n";
    echo "  ✓ Source: " . $customer->source_table . "\n";
} else {
    echo "  ✗ Customer not found\n";
}

// Test contractor
echo "\nContractor (ID: 1 from contractors table):\n";
$contractor = $userModel->getUserByIdAndTable(1, 'contractors');
if ($contractor) {
    echo "  ✓ Found: " . $contractor->name . "\n";
    echo "  ✓ Email: " . ($contractor->email ?? 'N/A') . "\n";
    echo "  ✓ Source: " . $contractor->source_table . "\n";
} else {
    echo "  ✗ Contractor not found\n";
}

echo "\n=== EDIT FUNCTIONALITY READY FOR TESTING ===\n";
echo "✓ getUserByIdAndTable method added\n";
echo "✓ updateUser method updated to handle source tables\n";
echo "✓ AdminController updated with source table parameter\n";
echo "✓ JavaScript updated to pass source table\n";
echo "✓ Form updated with hidden source table field\n";
echo "\nNow test the edit buttons on the users page!\n";
?>
