<?php
require_once 'bootstrap.php';

echo "=== CHECKING EXISTING DATA FOR API TESTING ===\n\n";

// Check customers
$customerModel = new Customer();
$customers = $customerModel->getCustomers();

echo "Customers in database:\n";
if ($customers) {
    foreach ($customers as $customer) {
        echo "- ID: {$customer->customer_id}, Email: {$customer->email}, Name: {$customer->name}\n";
    }
} else {
    echo "No customers found\n";
}

echo "\nContractors in database:\n";
$contractorModel = new Contractor();
$contractors = $contractorModel->getContractors();

if ($contractors) {
    foreach ($contractors as $contractor) {
        echo "- ID: {$contractor->contractor_id}, Email: {$contractor->email}, Name: {$contractor->name}\n";
    }
} else {
    echo "No contractors found\n";
}

echo "\n=== API SETUP RECOMMENDATIONS ===\n";
echo "1. Set passwords for existing users to test the mobile API\n";
echo "2. Use the emails shown above in mobile-api/test-api.php\n";
echo "3. Your mobile API is ready for React Native/Flutter integration\n";
echo "4. Check mobile-api/README.md for complete documentation\n";
?>