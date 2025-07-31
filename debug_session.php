<?php
// Start session first, then include bootstrap
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include bootstrap without starting session again
require_once 'app/config.php';
require_once 'app/Database.php';

echo "<h1>Session Debug Information</h1>";
echo "<h2>Current Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check if user exists in database
if (isset($_SESSION['user_id'])) {
    $db = new Database();

    // First check if roles table exists and what columns it has
    echo "<h2>Roles Table Structure:</h2>";
    try {
        $db->query('DESCRIBE roles');
        $roleStructure = $db->resultSet();
        echo "<pre>";
        print_r($roleStructure);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error checking roles table: " . $e->getMessage();
    }

    // Check users table structure  
    echo "<h2>Users Table Structure:</h2>";
    try {
        $db->query('DESCRIBE users');
        $userStructure = $db->resultSet();
        echo "<pre>";
        print_r($userStructure);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error checking users table: " . $e->getMessage();
    }

    // Get current user data
    echo "<h2>Current User Database Information:</h2>";
    try {
        $db->query('
            SELECT u.*, r.name as role_name, r.role_name as role_display_name
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.role_id 
            WHERE u.user_id = :user_id
        ');
        $db->bind(':user_id', $_SESSION['user_id']);
        $user = $db->single();
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();

        // Try alternative query
        echo "<h3>Trying alternative query...</h3>";
        try {
            $db->query('SELECT * FROM users WHERE user_id = :user_id');
            $db->bind(':user_id', $_SESSION['user_id']);
            $user = $db->single();
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } catch (Exception $e2) {
            echo "Alternative query also failed: " . $e2->getMessage();
        }
    }
} else {
    echo "<h2>No user logged in</h2>";
}

echo "<h2>Available Roles in Database:</h2>";
$db = new Database();
try {
    $db->query('SELECT * FROM roles');
    $roles = $db->resultSet();
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error fetching roles: " . $e->getMessage();
}

echo "<h2>All Users with Roles:</h2>";
try {
    $db->query('
        SELECT u.user_id, u.username, u.role_id, r.role_name, r.name
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id
        ORDER BY u.user_id
    ');
    $users = $db->resultSet();
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();

    // Try simpler query
    echo "<h3>Trying simpler user query...</h3>";
    try {
        $db->query('SELECT * FROM users ORDER BY user_id');
        $users = $db->resultSet();
        echo "<pre>";
        print_r($users);
        echo "</pre>";
    } catch (Exception $e2) {
        echo "Simple user query failed: " . $e2->getMessage();
    }
}
?>