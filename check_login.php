<?php
session_start();
require_once 'bootstrap.php';

echo "<h3>Login Status Check</h3>";

if (function_exists('isLoggedIn')) {
    if (isLoggedIn()) {
        echo "<p>✅ You are logged in</p>";
        echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "</p>";
        echo "<p>Username: " . ($_SESSION['user_name'] ?? 'Not set') . "</p>";
    } else {
        echo "<p>❌ You are NOT logged in</p>";
        echo "<p><a href='" . URLROOT . "/users/login'>Click here to login</a></p>";
    }
} else {
    echo "<p>❌ isLoggedIn function not found</p>";
}

echo "<h4>Session Data:</h4>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";
?>
