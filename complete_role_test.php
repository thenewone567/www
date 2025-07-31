<?php
session_start();
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/User.php';

echo "<h1>Complete Role System Test</h1>";

$userModel = new User();
$db = new Database();

// 1. Check role table structure and data
echo "<h2>1. Role System Status</h2>";

// Check both schemas
$adminPanelSchema = false;
$enhancementSchema = false;

try {
    $db->query('SELECT id, name FROM roles LIMIT 1');
    $test = $db->single();
    if ($test) {
        $adminPanelSchema = true;
        echo "✅ Admin Panel Schema detected (id, name)<br>";
    }
} catch (Exception $e) {
    // Not admin panel schema
}

try {
    $db->query('SELECT role_id, role_name FROM roles LIMIT 1');
    $test = $db->single();
    if ($test) {
        $enhancementSchema = true;
        echo "✅ Enhancement Schema detected (role_id, role_name)<br>";
    }
} catch (Exception $e) {
    // Not enhancement schema
}

if ($adminPanelSchema) {
    echo "<h3>Roles (Admin Panel Schema):</h3>";
    $db->query('SELECT * FROM roles ORDER BY id');
    $roles = $db->resultSet();
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
} elseif ($enhancementSchema) {
    echo "<h3>Roles (Enhancement Schema):</h3>";
    $db->query('SELECT * FROM roles ORDER BY role_id');
    $roles = $db->resultSet();
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
}

// 2. Check sukhdev's data
echo "<h2>2. Sukhdev User Test</h2>";
$db->query('SELECT user_id, username, role_id FROM users WHERE username = :username');
$db->bind(':username', 'sukhdev');
$sukhdev = $db->single();

if ($sukhdev) {
    echo "<h3>Sukhdev's Database Info:</h3>";
    echo "<pre>";
    print_r($sukhdev);
    echo "</pre>";

    echo "<h3>Sukhdev with Role Info (via getUserWithRole):</h3>";
    $sukhdevWithRole = $userModel->getUserWithRole($sukhdev->user_id);
    echo "<pre>";
    print_r($sukhdevWithRole);
    echo "</pre>";
} else {
    echo "❌ Sukhdev not found<br>";
}

// 3. Test login and session creation
echo "<h2>3. Login and Session Test</h2>";

// Clear session first
session_unset();

$loginResult = $userModel->login('sukhdev', 'password123');
if ($loginResult) {
    echo "✅ Login successful<br>";

    // Create session manually (like UsersController does)
    $userWithRole = $userModel->getUserWithRole($loginResult->user_id);

    $_SESSION['user_id'] = $loginResult->user_id;
    $_SESSION['user_username'] = $loginResult->username;
    $_SESSION['user_name'] = $loginResult->username;
    $_SESSION['user_role'] = $userWithRole->role_name ?? 'employee';
    $_SESSION['role_id'] = $userWithRole->role_id ?? 3;

    echo "<h3>Created Session:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    // Test Admin Settings visibility
    echo "<h3>Admin Settings Visibility Test:</h3>";
    $showAdminSettings = ($_SESSION['user_role'] === 'admin');
    echo "user_role = '{$_SESSION['user_role']}'<br>";
    echo "Show Admin Settings? " . ($showAdminSettings ? '✅ YES' : '❌ NO') . "<br>";

    if (!$showAdminSettings) {
        echo "<strong>❌ Problem: Admin Settings will not show for sukhdev!</strong><br>";
        echo "Expected user_role = 'admin', but got user_role = '{$_SESSION['user_role']}'<br>";

        // Check what the role should be
        if ($sukhdev && $sukhdev->role_id == 1) {
            echo "Sukhdev has role_id = 1, which should be admin role<br>";

            if ($adminPanelSchema) {
                $db->query('SELECT * FROM roles WHERE id = 1');
                $role = $db->single();
                echo "Role ID 1 in database: <pre>";
                print_r($role);
                echo "</pre>";
            }
        }
    } else {
        echo "✅ Admin Settings will show correctly!<br>";
    }

} else {
    echo "❌ Login failed<br>";
}

// 4. Fix recommendation
echo "<h2>4. Fix Recommendations</h2>";

if (!$showAdminSettings && $sukhdev && $sukhdev->role_id == 1) {
    echo "🔧 The issue appears to be with role name mapping.<br>";
    echo "Sukhdev has role_id = 1, but getUserWithRole is not returning role_name = 'admin'<br>";

    // Test both schemas manually
    if ($adminPanelSchema) {
        echo "<h4>Testing admin panel schema query:</h4>";
        try {
            $db->query('SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.user_id = :user_id');
            $db->bind(':user_id', $sukhdev->user_id);
            $test = $db->single();
            echo "<pre>";
            print_r($test);
            echo "</pre>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    }

    if ($enhancementSchema) {
        echo "<h4>Testing enhancement schema query:</h4>";
        try {
            $db->query('SELECT u.*, r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = :user_id');
            $db->bind(':user_id', $sukhdev->user_id);
            $test = $db->single();
            echo "<pre>";
            print_r($test);
            echo "</pre>";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    }
}

?>