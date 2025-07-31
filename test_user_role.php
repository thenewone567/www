<?php
session_start();
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/User.php';

$userModel = new User();

echo "<h1>User Role Testing</h1>";

// Test getUserWithRole for the current user in session
if (isset($_SESSION['user_id'])) {
    echo "<h2>Testing getUserWithRole for session user (ID: {$_SESSION['user_id']}):</h2>";
    $userWithRole = $userModel->getUserWithRole($_SESSION['user_id']);
    echo "<pre>";
    print_r($userWithRole);
    echo "</pre>";
} else {
    echo "<h2>No user in session</h2>";
}

// Test with sukhdev specifically
echo "<h2>Testing with sukhdev user:</h2>";
$db = new Database();

// First find sukhdev's user_id
$db->query('SELECT user_id, username, role_id FROM users WHERE username = :username');
$db->bind(':username', 'sukhdev');
$sukhdev = $db->single();

if ($sukhdev) {
    echo "<h3>Sukhdev's basic info:</h3>";
    echo "<pre>";
    print_r($sukhdev);
    echo "</pre>";

    echo "<h3>Sukhdev with role info:</h3>";
    $sukhdevWithRole = $userModel->getUserWithRole($sukhdev->user_id);
    echo "<pre>";
    print_r($sukhdevWithRole);
    echo "</pre>";

    // Test what role_name should be
    echo "<h3>Expected role for role_id {$sukhdev->role_id}:</h3>";

    // Try both schemas
    try {
        $db->query('SELECT * FROM roles WHERE id = :role_id');
        $db->bind(':role_id', $sukhdev->role_id);
        $role = $db->single();
        if ($role) {
            echo "Admin panel schema role: <pre>";
            print_r($role);
            echo "</pre>";
        }
    } catch (Exception $e) {
        echo "Admin panel schema failed: " . $e->getMessage() . "<br>";
    }

    try {
        $db->query('SELECT * FROM roles WHERE role_id = :role_id');
        $db->bind(':role_id', $sukhdev->role_id);
        $role = $db->single();
        if ($role) {
            echo "Enhancement schema role: <pre>";
            print_r($role);
            echo "</pre>";
        }
    } catch (Exception $e) {
        echo "Enhancement schema failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Sukhdev user not found in database!<br>";
}

?>