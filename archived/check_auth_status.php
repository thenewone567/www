<?php
// Check if user is actually logged in when accessing bot dashboard
session_start();

echo "🔍 Authentication Status Check\n";
echo "===============================\n\n";

echo "1. 📋 Session Information:\n";
echo "   Session ID: " . session_id() . "\n";
echo "   Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";

if (isset($_SESSION)) {
    echo "   Session data exists: Yes\n";
    echo "   Session keys: " . implode(', ', array_keys($_SESSION)) . "\n";

    if (isset($_SESSION['user_id'])) {
        echo "   User ID: " . $_SESSION['user_id'] . "\n";
    } else {
        echo "   User ID: Not set\n";
    }

    if (isset($_SESSION['username'])) {
        echo "   Username: " . $_SESSION['username'] . "\n";
    } else {
        echo "   Username: Not set\n";
    }

    if (isset($_SESSION['role'])) {
        echo "   Role: " . $_SESSION['role'] . "\n";
    } else {
        echo "   Role: Not set\n";
    }
} else {
    echo "   Session data: None\n";
}

echo "\n2. 🔑 Authentication Function Check:\n";

// Include the functions to check auth
require_once 'app/helpers.php';

if (function_exists('isLoggedIn')) {
    echo "   isLoggedIn(): " . (isLoggedIn() ? 'TRUE (User is logged in)' : 'FALSE (User not logged in)') . "\n";
} else {
    echo "   isLoggedIn() function: Not found\n";
}

if (function_exists('hasPermission')) {
    echo "   hasPermission('admin'): " . (hasPermission('admin') ? 'TRUE' : 'FALSE') . "\n";
    echo "   hasPermission('bot'): " . (hasPermission('bot') ? 'TRUE' : 'FALSE') . "\n";
} else {
    echo "   hasPermission() function: Not found\n";
}

echo "\n3. 🌐 Direct Bot Controller Access Test:\n";

try {
    // Test what happens when we try to access BotController directly
    require_once 'bootstrap.php';

    // This should trigger the constructor which checks auth
    echo "   Creating BotController instance...\n";
    $botController = new BotController();
    echo "   ✅ BotController created successfully (user has access)\n";

    // Test executeAction method
    echo "   Testing executeAction method...\n";
    $_POST['bot_id'] = 'sales_bot';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    ob_start();
    $botController->executeAction();
    $output = ob_get_clean();

    echo "   executeAction output: " . substr($output, 0, 200) . "\n";

    if (strpos($output, 'success') !== false) {
        echo "   ✅ Bot execution successful\n";
    } else {
        echo "   ❌ Bot execution failed\n";
    }

} catch (Exception $e) {
    echo "   ❌ BotController access failed: " . $e->getMessage() . "\n";

    if (strpos($e->getMessage(), 'redirect') !== false || strpos($e->getMessage(), 'login') !== false) {
        echo "   🔒 Authentication blocking access\n";
    }
}

echo "\n4. 🎯 Summary:\n";
echo "   This will help identify if:\n";
echo "   - User is actually logged in when seeing fake messages\n";
echo "   - Authentication is working properly\n";
echo "   - Bot dashboard somehow bypasses authentication\n";
echo "\n";
?>