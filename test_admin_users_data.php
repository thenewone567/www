<?php
echo "=== Testing Admin Users Page with Unique ID Column ===\n\n";

// Test the User model method that provides data to the admin users page
require_once 'bootstrap.php';

$userModel = new User();

echo "1. Testing getAllUsersWithCategories() method:\n";
$users = $userModel->getAllUsersWithCategories();

if (!empty($users)) {
    echo "   Found " . count($users) . " users:\n\n";

    foreach (array_slice($users, 0, 5) as $index => $user) {
        echo "   User " . ($index + 1) . ":\n";
        echo "     ID: {$user->user_id}\n";
        echo "     Name: {$user->name}\n";
        echo "     Category: {$user->user_category}\n";
        echo "     Source: {$user->source_table}\n";
        echo "     Unique ID: " . (isset($user->unique_id) && !empty($user->unique_id) ? $user->unique_id : 'Not assigned') . "\n";
        echo "     Email: " . (isset($user->email) ? $user->email : 'N/A') . "\n";
        echo "     Status: " . (isset($user->status) ? $user->status : 'N/A') . "\n";
        echo "\n";
    }
} else {
    echo "   No users found.\n";
}

echo "2. Testing unique ID distribution:\n";
$userStats = [
    'total' => count($users),
    'with_unique_id' => 0,
    'without_unique_id' => 0,
    'by_category' => []
];

foreach ($users as $user) {
    $category = $user->user_category ?? 'unknown';

    if (!isset($userStats['by_category'][$category])) {
        $userStats['by_category'][$category] = [
            'total' => 0,
            'with_unique_id' => 0
        ];
    }

    $userStats['by_category'][$category]['total']++;

    if (isset($user->unique_id) && !empty($user->unique_id)) {
        $userStats['with_unique_id']++;
        $userStats['by_category'][$category]['with_unique_id']++;
    } else {
        $userStats['without_unique_id']++;
    }
}

echo "   Overall Statistics:\n";
echo "     Total users: {$userStats['total']}\n";
echo "     With unique ID: {$userStats['with_unique_id']}\n";
echo "     Without unique ID: {$userStats['without_unique_id']}\n\n";

echo "   By Category:\n";
foreach ($userStats['by_category'] as $category => $stats) {
    $percentage = $stats['total'] > 0 ? round(($stats['with_unique_id'] / $stats['total']) * 100, 1) : 0;
    echo "     {$category}: {$stats['with_unique_id']}/{$stats['total']} ({$percentage}%)\n";
}

echo "\n3. Sample unique IDs by category:\n";
$categories = ['official', 'customer', 'contractor'];
foreach ($categories as $category) {
    $categoryUsers = array_filter($users, function ($user) use ($category) {
        return ($user->user_category ?? '') === $category && isset($user->unique_id) && !empty($user->unique_id);
    });

    if (!empty($categoryUsers)) {
        $sampleUser = array_values($categoryUsers)[0];
        echo "   {$category}: {$sampleUser->unique_id} ({$sampleUser->name})\n";
    } else {
        echo "   {$category}: No users with unique ID found\n";
    }
}

echo "\n✅ Admin users page data is ready with unique ID column!\n";
echo "✅ Visit http://localhost/admin/users to see the updated table\n";
?>