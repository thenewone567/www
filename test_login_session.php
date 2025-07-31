<?php
session_start();
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/User.php';
require_once 'app/controllers/UsersController.php';

$userModel = new User();
$usersController = new UsersController();

echo "<h1>Test Login Session Creation</h1>";

// Simulate login for sukhdev
echo "<h2>Simulating login for sukhdev...</h2>";

$loginResult = $userModel->login('sukhdev', 'password123');

if ($loginResult) {
    echo "<h3>Login successful! User data:</h3>";
    echo "<pre>";
    print_r($loginResult);
    echo "</pre>";

    // Test createUserSession
    echo "<h3>Creating user session...</h3>";

    // Clear existing session first
    session_unset();

    // Manually call createUserSession (without redirect)
    if (isset($loginResult->user_id) && isset($loginResult->username)) {
        // Get user role information
        $userWithRole = $userModel->getUserWithRole($loginResult->user_id);

        echo "<h4>User with role data:</h4>";
        echo "<pre>";
        print_r($userWithRole);
        echo "</pre>";

        $_SESSION['user_id'] = $loginResult->user_id;
        $_SESSION['user_username'] = $loginResult->username;
        $_SESSION['user_name'] = $loginResult->username;
        $_SESSION['user_role'] = $userWithRole->role_name ?? 'employee';
        $_SESSION['role_id'] = $userWithRole->role_id ?? 3;

        echo "<h4>Session data after creation:</h4>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";

        // Test if Admin Settings should show
        echo "<h3>Admin Settings Test:</h3>";
        echo "user_role = '" . $_SESSION['user_role'] . "'<br>";
        echo "Should show Admin Settings? " . ($_SESSION['user_role'] === 'admin' ? 'YES ✅' : 'NO ❌') . "<br>";

    } else {
        echo "❌ Invalid user session data<br>";
    }

} else {
    echo "❌ Login failed for sukhdev<br>";

    // Check if user exists
    $user = $userModel->findUserByUsername('sukhdev');
    if ($user) {
        echo "User exists but password might be wrong. User data:<br>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "User 'sukhdev' not found in database<br>";
    }
}

?>