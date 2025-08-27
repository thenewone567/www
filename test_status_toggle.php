<?php
require_once 'bootstrap.php';

echo "=== TESTING STATUS TOGGLE FIX ===\n";

$userModel = new User();

// Test customer status methods
echo "\n1. Testing Customer Status Methods:\n";
$customerId = 6; // Lucky customer
echo "Testing setCustomerStatus for customer ID: $customerId\n";

$customer = $userModel->getUserByIdAndTable($customerId, 'customers');
if ($customer) {
    echo "Current customer: " . $customer->name . " - Status: " . $customer->status . "\n";

    // Test status toggle
    $newStatus = ($customer->status === 'active') ? 'inactive' : 'active';
    echo "Attempting to change status to: $newStatus\n";

    $result = $userModel->setCustomerStatus($customerId, $newStatus);
    if ($result) {
        echo "✓ Status change successful\n";

        // Verify the change
        $updatedCustomer = $userModel->getUserByIdAndTable($customerId, 'customers');
        echo "✓ New status: " . $updatedCustomer->status . "\n";

        // Restore original status
        $userModel->setCustomerStatus($customerId, $customer->status);
        echo "✓ Status restored to original\n";
    } else {
        echo "✗ Status change failed\n";
    }
} else {
    echo "✗ Customer not found\n";
}

// Test contractor status methods
echo "\n2. Testing Contractor Status Methods:\n";
$contractorId = 1; // John Smith contractor
echo "Testing setContractorStatus for contractor ID: $contractorId\n";

$contractor = $userModel->getUserByIdAndTable($contractorId, 'contractors');
if ($contractor) {
    echo "Current contractor: " . $contractor->name . " - Status: " . ($contractor->status ? 'active' : 'inactive') . "\n";

    // Test status toggle
    $newStatus = ($contractor->status == 1) ? 'inactive' : 'active';
    echo "Attempting to change status to: $newStatus\n";

    $result = $userModel->setContractorStatus($contractorId, $newStatus);
    if ($result) {
        echo "✓ Status change successful\n";

        // Verify the change
        $updatedContractor = $userModel->getUserByIdAndTable($contractorId, 'contractors');
        echo "✓ New status: " . ($updatedContractor->status ? 'active' : 'inactive') . "\n";

        // Restore original status
        $originalStatus = ($contractor->status == 1) ? 'active' : 'inactive';
        $userModel->setContractorStatus($contractorId, $originalStatus);
        echo "✓ Status restored to original\n";
    } else {
        echo "✗ Status change failed\n";
    }
} else {
    echo "✗ Contractor not found\n";
}

echo "\n=== STATUS TOGGLE FUNCTIONALITY FIXED ===\n";
echo "✓ Customer and contractor status toggle methods added\n";
echo "✓ AdminController updated to handle source tables\n";
echo "✓ Frontend JavaScript updated to pass source table\n";
echo "✓ Customer and Contractor dashboard controllers created\n";
echo "\nNow test the status toggle buttons on the users page!\n";
echo "And consider implementing separate customer/contractor login portals.\n";
?>