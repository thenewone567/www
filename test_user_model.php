<?php
// Include the bootstrap file to initialize the framework
require_once 'bootstrap.php';

// Test the User model getAllUsersWithCategories method
$userModel = new User();

echo "=== TESTING getAllUsersWithCategories() ===\n";

try {
    $users = $userModel->getAllUsersWithCategories();
    
    echo "Total users found: " . count($users) . "\n\n";
    
    $officials = 0;
    $customers = 0;
    $contractors = 0;
    
    foreach ($users as $user) {
        echo "User: " . $user->name . "\n";
        echo "  Category: " . $user->user_category . "\n";
        echo "  Role: " . $user->role_name . "\n";
        echo "  Email: " . ($user->email ?? 'N/A') . "\n";
        echo "  Phone: " . ($user->phone ?? 'N/A') . "\n";
        echo "  Status: " . ($user->status ?? $user->customer_status ?? 'N/A') . "\n";
        echo "  Source: " . $user->source_table . "\n";
        echo "---\n";
        
        switch ($user->user_category) {
            case 'official':
                $officials++;
                break;
            case 'customer':
                $customers++;
                break;
            case 'contractor':
                $contractors++;
                break;
        }
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Officials: $officials\n";
    echo "Customers: $customers\n";
    echo "Contractors: $contractors\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
