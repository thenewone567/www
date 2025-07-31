<?php
require_once 'app/config.php';
require_once 'app/Database.php';

echo "<h1>Navigation Links Test</h1>";

// Test URLs that should work
$urls_to_test = [
    'Profile' => URLROOT . '/users/profile',
    'Change Password' => URLROOT . '/users/changePassword',
    'Notifications' => URLROOT . '/notifications',
    'Admin Panel' => URLROOT . '/admin',
    'Logout' => URLROOT . '/users/logout'
];

echo "<h2>Available Links:</h2>";
echo "<ul>";
foreach ($urls_to_test as $name => $url) {
    echo "<li><a href='$url' target='_blank'>$name</a> - $url</li>";
}
echo "</ul>";

echo "<h2>Current Session Status:</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<p>✅ User logged in:</p>";
    echo "<ul>";
    echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
    echo "<li>Username: " . $_SESSION['user_username'] . "</li>";
    echo "<li>Role: " . $_SESSION['user_role'] . "</li>";
    echo "</ul>";

    echo "<h3>Header Button Logic:</h3>";
    if ($_SESSION['user_role'] === 'admin') {
        echo "✅ Should show: <strong>Admin Settings</strong> button";
    } else {
        echo "Should show: <strong>Toggle Menu</strong> button";
    }
} else {
    echo "<p>❌ No user logged in</p>";
    echo "<p><a href='" . URLROOT . "/users/login'>Login here</a></p>";
}

echo "<h2>Controllers Check:</h2>";
$controllers = [
    'UsersController' => 'app/controllers/UsersController.php',
    'NotificationsController' => 'app/controllers/NotificationsController.php',
    'AdminController' => 'app/controllers/AdminController.php'
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists<br>";
    } else {
        echo "❌ $name missing<br>";
    }
}

echo "<h2>Views Check:</h2>";
$views = [
    'Profile View' => 'app/views/users/profile.php',
    'Change Password View' => 'app/views/users/changePassword.php',
    'Notifications View' => 'app/views/notifications/index.php'
];

foreach ($views as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name exists<br>";
    } else {
        echo "❌ $name missing<br>";
    }
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    h1,
    h2,
    h3 {
        color: #333;
    }

    ul {
        margin: 10px 0;
    }

    li {
        margin: 5px 0;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>