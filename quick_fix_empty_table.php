<?php
require_once 'bootstrap.php';

echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .test-panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin: 15px 0; }
    .success { background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .error { background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .info { background-color: #d1ecf1; color: #0c5460; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .warning { background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 5px; margin: 8px 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 11px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 12px; }
    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
    th { background-color: #f8f9fa; }
    .btn { background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; }
    .btn-success { background: #28a745; }
    .btn-danger { background: #dc3545; }
</style>";

echo "<h1>🔧 Direct Fix for Empty Table</h1>";

try {
    // Start session
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1;
        $_SESSION['role_name'] = 'Admin';
    }

    $db = new Database();

    echo "<div class='test-panel'>";
    echo "<h3>🔍 Quick Diagnosis</h3>";

    // Test 1: Simple user count
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    if ($result && $db->execute()) {
        $count = $db->single();
        echo "<div class='info'>Users table count: {$count->count}</div>";

        if ($count->count == 0) {
            echo "<div class='error'>❌ FOUND THE ISSUE: Users table is empty!</div>";
            echo "<div class='warning'>🛠️ Solution: Re-populate the users table</div>";

            // Create users right here
            echo "<h4>Creating default users now...</h4>";

            // Get admin role ID
            $adminRoleId = 1;
            $result = $db->query("SELECT role_id FROM roles WHERE role_name = 'Admin' LIMIT 1");
            if ($result && $db->execute()) {
                $role = $db->single();
                if ($role) {
                    $adminRoleId = $role->role_id;
                }
            }

            $defaultUsers = [
                [
                    'username'  => 'admin',
                    'email'     => 'admin@hardware-store.com',
                    'password'  => password_hash('admin123', PASSWORD_DEFAULT),
                    'full_name' => 'System Administrator',
                    'role_id'   => $adminRoleId,
                    'is_active' => 1
                ],
                [
                    'username'  => 'manager',
                    'email'     => 'manager@hardware-store.com',
                    'password'  => password_hash('manager123', PASSWORD_DEFAULT),
                    'full_name' => 'Store Manager',
                    'role_id'   => 2,
                    'is_active' => 1
                ],
                [
                    'username'  => 'cashier',
                    'email'     => 'cashier@hardware-store.com',
                    'password'  => password_hash('cashier123', PASSWORD_DEFAULT),
                    'full_name' => 'Store Cashier',
                    'role_id'   => 4,
                    'is_active' => 1
                ]
            ];

            foreach ($defaultUsers as $userData) {
                $sql = "INSERT INTO users (username, email, password_hash, full_name, role_id, is_active, created_at) 
                        VALUES (:username, :email, :password, :full_name, :role_id, :is_active, NOW())";

                $result = $db->query($sql);
                $db->bind(':username', $userData['username']);
                $db->bind(':email', $userData['email']);
                $db->bind(':password', $userData['password']);
                $db->bind(':full_name', $userData['full_name']);
                $db->bind(':role_id', $userData['role_id']);
                $db->bind(':is_active', $userData['is_active']);

                if ($db->execute()) {
                    echo "<div class='success'>✅ Created user: {$userData['username']}</div>";
                } else {
                    echo "<div class='error'>❌ Failed to create user: {$userData['username']}</div>";
                }
            }

        } else {
            echo "<div class='success'>✅ Users table has data ({$count->count} users)</div>";
        }
    }
    echo "</div>";

    echo "<div class='test-panel'>";
    echo "<h3>🧪 Test User Model Methods</h3>";

    try {
        $userModel = new User();

        // Test getAllUsersWithRoles
        echo "<h4>Testing getAllUsersWithRoles():</h4>";
        $officials = $userModel->getAllUsersWithRoles();
        echo "<div class='info'>Result: " . count($officials) . " officials found</div>";

        if (count($officials) > 0) {
            echo "<div class='success'>✅ getAllUsersWithRoles() is working</div>";

            // Show sample
            echo "<h5>Sample Official User:</h5>";
            echo "<pre>" . json_encode($officials[0], JSON_PRETTY_PRINT) . "</pre>";
        } else {
            echo "<div class='error'>❌ getAllUsersWithRoles() returns empty - investigating...</div>";

            // Test the raw SQL
            echo "<h5>Testing raw SQL query:</h5>";
            $sql = "SELECT u.user_id, u.full_name AS name, u.username, u.email, u.role_id, u.is_active AS status, 
                           COALESCE(r.role_name, 'User') AS role_name, 'official' AS user_category,
                           u.created_at AS created_at, NULL AS last_login
                    FROM users u 
                    LEFT JOIN roles r ON u.role_id = r.role_id 
                    ORDER BY u.user_id DESC";

            $result = $db->query($sql);
            if ($result && $db->execute()) {
                $directUsers = $db->resultSet();
                echo "<div class='info'>Direct SQL result: " . count($directUsers) . " users</div>";

                if (count($directUsers) > 0) {
                    echo "<div class='warning'>⚠️ Direct SQL works but method doesn't - there's a bug in getAllUsersWithRoles()</div>";
                    echo "<h5>Sample from direct query:</h5>";
                    echo "<pre>" . json_encode($directUsers[0], JSON_PRETTY_PRINT) . "</pre>";
                } else {
                    echo "<div class='error'>❌ Direct SQL also returns empty</div>";
                }
            }
        }

        // Test getAllUsersWithCategories
        echo "<h4>Testing getAllUsersWithCategories():</h4>";
        $allUsers = $userModel->getAllUsersWithCategories();
        echo "<div class='info'>Result: " . count($allUsers) . " total users found</div>";

        if (count($allUsers) > 0) {
            echo "<div class='success'>✅ getAllUsersWithCategories() is working</div>";

            // Categorize
            $categories = ['official' => 0, 'customer' => 0, 'contractor' => 0];
            foreach ($allUsers as $user) {
                $category = $user->user_category ?? 'official';
                if (isset($categories[$category])) {
                    $categories[$category]++;
                }
            }

            echo "<table>";
            echo "<tr><th>Category</th><th>Count</th></tr>";
            foreach ($categories as $cat => $count) {
                echo "<tr><td>{$cat}</td><td>{$count}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error'>❌ getAllUsersWithCategories() returns empty</div>";
        }

    } catch (Exception $e) {
        echo "<div class='error'>❌ User Model Error: " . $e->getMessage() . "</div>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    echo "</div>";

    echo "<div class='test-panel'>";
    echo "<h3>🚀 Quick Fixes</h3>";

    echo "<div class='warning'>";
    echo "<h4>If the table is still empty, try these solutions:</h4>";
    echo "<ol>";
    echo "<li><strong>Re-create users:</strong> <a href='setup_users_database.php' class='btn btn-success'>Run Database Setup</a></li>";
    echo "<li><strong>Test admin page:</strong> <a href='admin/users' class='btn'>Check Admin Users Page</a></li>";
    echo "<li><strong>Clear browser cache:</strong> Hard refresh the admin page (Ctrl+F5)</li>";
    echo "<li><strong>Check database connection:</strong> Ensure WAMP MySQL is running</li>";
    echo "</ol>";
    echo "</div>";

    // Final verification
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    if ($result && $db->execute()) {
        $count = $db->single();
        if ($count->count > 0) {
            echo "<div class='success'>";
            echo "<h4>✅ Status: FIXED</h4>";
            echo "<p>Users table now has {$count->count} users. The admin page should work now.</p>";
            echo "<a href='admin/users' class='btn btn-success'>🔗 Test Admin Users Page</a>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h4>❌ Status: STILL BROKEN</h4>";
            echo "<p>Users table is still empty. Manual intervention needed.</p>";
            echo "</div>";
        }
    }
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='test-panel'>";
    echo "<div class='error'>";
    echo "<h3>❌ Critical Error</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Debug completed:</strong> Check the results above and follow the recommended fixes.</p>";
?>