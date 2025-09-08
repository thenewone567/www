<?php
require_once 'bootstrap.php';

echo "Testing Company model...\n";

try {
    $company = new Company();
    
    // Test getCompany
    echo "Testing getCompany(1)...\n";
    $result = $company->getCompany(1);
    
    if ($result) {
        echo "Company found:\n";
        print_r($result);
    } else {
        echo "No company found\n";
    }
    
    // Test saveCompany
    echo "\nTesting saveCompany...\n";
    $testData = [
        'company_name' => 'Test Company Update',
        'address' => 'Test Address',
        'phone' => '123-456-7890',
        'email' => 'test@example.com',
        'tax_number' => 'TEST123',
        'logo_path' => 'test/logo.png'
    ];
    
    $saveResult = $company->saveCompany(1, $testData);
    
    if ($saveResult) {
        echo "Save successful!\n";
        
        // Verify the save
        $updated = $company->getCompany(1);
        echo "Updated company data:\n";
        print_r($updated);
        
    } else {
        echo "Save failed!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
