<?php
require_once 'bootstrap.php';

echo "Quick test of admin users data:\n";

$userModel = new User();
$users = $userModel->getAllUsersWithCategories();

echo "Found " . count($users) . " users\n";

if (!empty($users)) {
    $first = $users[0];
    echo "First user:\n";
    echo "  Name: " . $first->name . "\n";
    echo "  Category: " . $first->user_category . "\n";
    echo "  Unique ID: " . (isset($first->unique_id) ? $first->unique_id : 'NOT SET') . "\n";

    // Check a few more users
    $withUniqueId = 0;
    foreach (array_slice($users, 0, 5) as $user) {
        if (isset($user->unique_id) && !empty($user->unique_id)) {
            $withUniqueId++;
        }
    }
    echo "Out of first 5 users, $withUniqueId have unique IDs\n";
}

echo "✅ Test complete - check http://localhost/admin/users\n";
?>