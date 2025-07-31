<?php
/**
 * Admin Panel Test Script
 * 
 * This script tests the admin panel functionality including:
 * - Database tables and relationships
 * - User and role management
 * - Permission systems
 * - Activity logging
 * - Settings management
 */

require_once 'bootstrap.php';

echo "<h1>Admin Panel Test Results</h1>\n";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.test { margin: 10px 0; padding: 10px; border-radius: 5px; }
.pass { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
</style>";

$db = new Database();

// Test 1: Check if required tables exist
echo "<h2>Database Tables Test</h2>\n";
$requiredTables = ['users', 'roles', 'user_activity_log', 'system_settings', 'notifications'];
$missingTables = [];

foreach ($requiredTables as $table) {
    try {
        $db->query("DESCRIBE $table");
        $result = $db->resultSet();
        if ($result) {
            echo "<div class='test pass'>✓ Table '$table' exists</div>\n";
        } else {
            echo "<div class='test fail'>✗ Table '$table' is empty or has issues</div>\n";
            $missingTables[] = $table;
        }
    } catch (Exception $e) {
        echo "<div class='test fail'>✗ Table '$table' does not exist</div>\n";
        $missingTables[] = $table;
    }
}

// Test 2: Check if admin user exists
echo "<h2>Admin User Test</h2>\n";
try {
    $db->query("SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE r.name = 'admin' LIMIT 1");
    $adminUser = $db->single();

    if ($adminUser) {
        echo "<div class='test pass'>✓ Admin user found: {$adminUser->name} ({$adminUser->email})</div>\n";
        echo "<div class='test info'>ℹ Role: {$adminUser->role_name}</div>\n";
    } else {
        echo "<div class='test fail'>✗ No admin user found</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Error checking admin user: " . $e->getMessage() . "</div>\n";
}

// Test 3: Check roles and permissions
echo "<h2>Roles and Permissions Test</h2>\n";
try {
    $db->query("SELECT * FROM roles ORDER BY id");
    $roles = $db->resultSet();

    if ($roles) {
        foreach ($roles as $role) {
            echo "<div class='test pass'>✓ Role: {$role->name} - {$role->description}</div>\n";

            // Check if permissions is valid JSON
            $permissions = json_decode($role->permissions, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<div class='test info'>ℹ Permissions: " . count($permissions) . " modules configured</div>\n";
            } else {
                echo "<div class='test fail'>✗ Invalid permissions JSON for role: {$role->name}</div>\n";
            }
        }
    } else {
        echo "<div class='test fail'>✗ No roles found</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Error checking roles: " . $e->getMessage() . "</div>\n";
}

// Test 4: Check activity logs
echo "<h2>Activity Logs Test</h2>\n";
try {
    $db->query("SELECT COUNT(*) as count FROM user_activity_log");
    $logCount = $db->single();

    if ($logCount && $logCount->count > 0) {
        echo "<div class='test pass'>✓ Activity logs table has {$logCount->count} entries</div>\n";

        // Get recent logs
        $db->query("SELECT action, COUNT(*) as count FROM user_activity_log GROUP BY action ORDER BY count DESC LIMIT 5");
        $actions = $db->resultSet();

        foreach ($actions as $action) {
            echo "<div class='test info'>ℹ Action '{$action->action}': {$action->count} times</div>\n";
        }
    } else {
        echo "<div class='test fail'>✗ No activity logs found</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Error checking activity logs: " . $e->getMessage() . "</div>\n";
}

// Test 5: Check system settings
echo "<h2>System Settings Test</h2>\n";
try {
    $db->query("SELECT COUNT(*) as count FROM system_settings");
    $settingsCount = $db->single();

    if ($settingsCount && $settingsCount->count > 0) {
        echo "<div class='test pass'>✓ System settings table has {$settingsCount->count} settings</div>\n";

        // Check for required settings
        $requiredSettings = ['site_name', 'timezone', 'auto_approve_threshold', 'low_stock_threshold'];
        foreach ($requiredSettings as $setting) {
            $db->query("SELECT * FROM system_settings WHERE setting_key = :key");
            $db->bind(':key', $setting);
            $result = $db->single();

            if ($result) {
                echo "<div class='test pass'>✓ Setting '{$setting}': {$result->setting_value}</div>\n";
            } else {
                echo "<div class='test fail'>✗ Missing required setting: {$setting}</div>\n";
            }
        }
    } else {
        echo "<div class='test fail'>✗ No system settings found</div>\n";
    }
} catch (Exception $e) {
    echo "<div class='test fail'>✗ Error checking system settings: " . $e->getMessage() . "</div>\n";
}

// Test 6: Check admin controller file
echo "<h2>Admin Controller Test</h2>\n";
$adminControllerPath = __DIR__ . '/app/controllers/AdminController.php';
if (file_exists($adminControllerPath)) {
    echo "<div class='test pass'>✓ AdminController.php exists</div>\n";

    // Check if class is properly defined
    $content = file_get_contents($adminControllerPath);
    if (strpos($content, 'class AdminController') !== false) {
        echo "<div class='test pass'>✓ AdminController class is defined</div>\n";
    } else {
        echo "<div class='test fail'>✗ AdminController class not found in file</div>\n";
    }

    // Check for required methods
    $requiredMethods = ['index', 'users', 'roles', 'activityLogs', 'settings'];
    foreach ($requiredMethods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "<div class='test pass'>✓ Method '$method' exists</div>\n";
        } else {
            echo "<div class='test fail'>✗ Method '$method' missing</div>\n";
        }
    }
} else {
    echo "<div class='test fail'>✗ AdminController.php file not found</div>\n";
}

// Test 7: Check admin view files
echo "<h2>Admin Views Test</h2>\n";
$adminViewsPath = __DIR__ . '/app/views/admin/';
$requiredViews = ['dashboard.php', 'users.php', 'roles.php', 'activity_logs.php', 'settings.php'];

foreach ($requiredViews as $view) {
    $viewPath = $adminViewsPath . $view;
    if (file_exists($viewPath)) {
        echo "<div class='test pass'>✓ Admin view '$view' exists</div>\n";
    } else {
        echo "<div class='test fail'>✗ Admin view '$view' missing</div>\n";
    }
}

// Test 8: User Model enhancements
echo "<h2>User Model Test</h2>\n";
$userModelPath = __DIR__ . '/app/models/User.php';
if (file_exists($userModelPath)) {
    $content = file_get_contents($userModelPath);

    // Check for admin-specific methods
    $requiredMethods = ['getAllUsersWithRoles', 'addUser', 'updateUser', 'logActivity', 'getActivityLogs'];
    foreach ($requiredMethods as $method) {
        if (strpos($content, "function $method") !== false) {
            echo "<div class='test pass'>✓ User model method '$method' exists</div>\n";
        } else {
            echo "<div class='test fail'>✗ User model method '$method' missing</div>\n";
        }
    }
} else {
    echo "<div class='test fail'>✗ User.php model file not found</div>\n";
}

// Summary
echo "<h2>Test Summary</h2>\n";
if (empty($missingTables)) {
    echo "<div class='test pass'>✓ All required database tables are present</div>\n";
} else {
    echo "<div class='test fail'>✗ Missing tables: " . implode(', ', $missingTables) . "</div>\n";
    echo "<div class='test info'>ℹ Please run the admin_panel_setup.sql script to create missing tables</div>\n";
}

echo "<div class='test info'>ℹ Access the admin panel at: <a href='/admin'>http://yoursite.com/admin</a></div>\n";
echo "<div class='test info'>ℹ Default admin login: admin@example.com / password</div>\n";
echo "<div class='test info'>ℹ Remember to change the default password after first login!</div>\n";

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>