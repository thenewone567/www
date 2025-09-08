<?php
require_once 'bootstrap.php';

echo "Testing company name integration...\n";

// Test company_name() helper
echo "Company name from helper: " . company_name() . "\n";

// Test company_logo() helper  
echo "Company logo from helper: " . company_logo() . "\n";

// Test that updating company name affects helper
echo "\nTesting company update...\n";
$company = new Company();
$originalCompany = $company->getCompany(1);
echo "Original company name: " . $originalCompany->company_name . "\n";

// Update company name
$updateResult = $company->saveCompany(1, ['company_name' => 'Updated Company Name Test']);
echo "Update result: " . ($updateResult ? 'Success' : 'Failed') . "\n";

// Test that helper returns updated name
echo "Company name after update: " . company_name() . "\n";

// Restore original name
$company->saveCompany(1, ['company_name' => $originalCompany->company_name]);
echo "Company name after restore: " . company_name() . "\n";

echo "\nIntegration test complete!\n";
?>
