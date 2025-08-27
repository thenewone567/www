<?php
require_once 'bootstrap.php';

echo "=== TESTING EDIT USER FUNCTIONALITY ===\n";

$userModel = new User();

// Test 1: Get a user from each table type
echo "\n1. Testing getUserById for different user types:\n";

// Get all users to find IDs
$allUsers = $userModel->getAllUsersWithCategories();
$testIds = [];

foreach ($allUsers as $user) {
    if (count($testIds) < 3) {
        if ($user->user_category === 'official' && !isset($testIds['official'])) {
            $testIds['official'] = $user->user_id;
        } elseif ($user->user_category === 'customer' && !isset($testIds['customer'])) {
            $testIds['customer'] = $user->user_id;
        } elseif ($user->user_category === 'contractor' && !isset($testIds['contractor'])) {
            $testIds['contractor'] = $user->user_id;
        }
    }
}

foreach ($testIds as $type => $id) {
    echo "\nTesting $type user (ID: $id):\n";
    $user = $userModel->getUserById($id);
    if ($user) {
        echo "  ✓ Found: " . $user->name . "\n";
        echo "  ✓ Email: " . ($user->email ?? 'N/A') . "\n";
        echo "  ✓ Category: " . $user->user_category . "\n";
        echo "  ✓ Source: " . $user->source_table . "\n";
    } else {
        echo "  ✗ User not found\n";
    }
}

// Test 2: Test updateUser method
echo "\n2. Testing updateUser functionality:\n";

if (isset($testIds['customer'])) {
    $customerId = $testIds['customer'];
    echo "\nTesting customer update (ID: $customerId):\n";
    
    $originalUser = $userModel->getUserById($customerId);
    if ($originalUser) {
        echo "  Original name: " . $originalUser->name . "\n";
        echo "  Original email: " . ($originalUser->email ?? 'N/A') . "\n";
        
        // Test update
        $updateData = [
            'user_id' => $customerId,
            'name' => $originalUser->name . " (Updated)",
            'email' => 'updated@test.com',
            'role_id' => 6, // Customer role
            'status' => 'active'
        ];
        
        $result = $userModel->updateUser($updateData);
        if ($result) {
            echo "  ✓ Update successful\n";
            
            // Verify the update
            $updatedUser = $userModel->getUserById($customerId);
            if ($updatedUser) {
                echo "  ✓ New name: " . $updatedUser->name . "\n";
                echo "  ✓ New email: " . ($updatedUser->email ?? 'N/A') . "\n";
                
                // Restore original data
                $restoreData = [
                    'user_id' => $customerId,
                    'name' => $originalUser->name,
                    'email' => $originalUser->email ?? '',
                    'role_id' => 6,
                    'status' => 'active'
                ];
                $userModel->updateUser($restoreData);
                echo "  ✓ Data restored\n";
            }
        } else {
            echo "  ✗ Update failed\n";
        }
    }
}

echo "\n=== TEST COMPLETE ===\n";
?>
