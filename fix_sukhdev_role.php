<?php
require_once 'app/config.php';
require_once 'app/Database.php';

$db = new Database();

echo "<h1>Fix Sukhdev's Role</h1>";

// Check current role setup
echo "<h2>Current Role Mappings:</h2>";
try {
    $db->query('SELECT * FROM roles ORDER BY id');
    $roles = $db->resultSet();
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error fetching roles: " . $e->getMessage() . "<br>";

    // Try alternative schema
    try {
        $db->query('SELECT * FROM roles ORDER BY role_id');
        $roles = $db->resultSet();
        echo "<pre>";
        print_r($roles);
        echo "</pre>";
    } catch (Exception $e2) {
        echo "Alternative schema also failed: " . $e2->getMessage() . "<br>";
    }
}

// Check sukhdev's current role
echo "<h2>Sukhdev's Current Info:</h2>";
$db->query('SELECT user_id, username, role_id FROM users WHERE username = :username');
$db->bind(':username', 'sukhdev');
$sukhdev = $db->single();

if ($sukhdev) {
    echo "<pre>";
    print_r($sukhdev);
    echo "</pre>";

    // Update sukhdev to admin role (role_id = 1)
    echo "<h3>Updating sukhdev to admin role (role_id = 1)...</h3>";

    $db->query('UPDATE users SET role_id = 1 WHERE username = :username');
    $db->bind(':username', 'sukhdev');

    if ($db->execute()) {
        echo "✅ Successfully updated sukhdev's role to admin (role_id = 1)<br>";

        // Verify the update
        $db->query('SELECT user_id, username, role_id FROM users WHERE username = :username');
        $db->bind(':username', 'sukhdev');
        $updatedUser = $db->single();
        echo "<h4>Updated info:</h4>";
        echo "<pre>";
        print_r($updatedUser);
        echo "</pre>";
    } else {
        echo "❌ Failed to update sukhdev's role<br>";
    }
} else {
    echo "❌ Sukhdev user not found<br>";
}

echo "<h2>All Users After Update:</h2>";
$db->query('SELECT user_id, username, role_id FROM users ORDER BY user_id');
$allUsers = $db->resultSet();
echo "<pre>";
print_r($allUsers);
echo "</pre>";

?>