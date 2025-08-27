<?php
require_once 'bootstrap.php';

// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
    .debug-panel { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin: 15px 0; }
    .success { background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .error { background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .info { background-color: #d1ecf1; color: #0c5460; padding: 12px; border-radius: 5px; margin: 8px 0; }
    .warning { background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 5px; margin: 8px 0; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f8f9fa; }
</style>";

echo "<h1>🚨 LIVE Role Dropdown Debug</h1>";

try {
    // Test 1: Direct Database Query
    echo "<div class='debug-panel'>";
    echo "<h3>1. Direct Database Query</h3>";

    $db = new Database();
    $result = $db->query("SELECT * FROM roles ORDER BY role_name");

    if ($result && $db->execute()) {
        $roles = $db->resultSet();
        echo "<div class='info'>Found " . count($roles) . " roles in database:</div>";

        if (count($roles) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Description</th></tr>";
            foreach ($roles as $role) {
                echo "<tr>";
                echo "<td>{$role->role_id}</td>";
                echo "<td><strong>" . htmlspecialchars($role->role_name) . "</strong></td>";
                echo "<td>" . htmlspecialchars($role->description ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error'>❌ No roles found in database!</div>";
        }
    } else {
        echo "<div class='error'>❌ Failed to query roles table</div>";
    }
    echo "</div>";

    // Test 2: Role Model Test
    echo "<div class='debug-panel'>";
    echo "<h3>2. Role Model Test</h3>";

    try {
        $roleModel = new Role();
        $modelRoles = $roleModel->getAllRoles();

        echo "<div class='info'>Role model returned " . count($modelRoles) . " roles:</div>";

        if ($modelRoles && count($modelRoles) > 0) {
            echo "<pre>";
            foreach ($modelRoles as $role) {
                echo "Role ID: {$role->role_id} | Name: {$role->role_name}\n";
            }
            echo "</pre>";
        } else {
            echo "<div class='error'>❌ Role model returned empty result</div>";
        }

    } catch (Exception $e) {
        echo "<div class='error'>❌ Role model error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 3: Simulate AdminController
    echo "<div class='debug-panel'>";
    echo "<h3>3. AdminController Simulation</h3>";

    try {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo "<div class='warning'>⚠️ No user logged in - simulating admin session</div>";
            $_SESSION['user_id'] = 1; // Temporary for testing
            $_SESSION['role_name'] = 'Admin';
        } else {
            echo "<div class='success'>✅ User logged in (ID: {$_SESSION['user_id']})</div>";
        }

        // Simulate what AdminController does
        $userModel = new User();
        $roleModel = new Role();

        // Get roles like AdminController would
        $roles = $roleModel->getAllRoles();

        echo "<div class='info'>AdminController would get " . count($roles) . " roles</div>";

        // Simulate the data array passed to view
        $data = [
            'title' => 'User Management',
            'roles' => $roles
        ];

        echo "<div class='info'>Data array that would be passed to view:</div>";
        echo "<pre>";
        echo "Array (\n";
        echo "    [title] => User Management\n";
        echo "    [roles] => Array (\n";
        if ($roles) {
            foreach ($roles as $index => $role) {
                echo "        [{$index}] => Object (\n";
                echo "            [role_id] => {$role->role_id}\n";
                echo "            [role_name] => {$role->role_name}\n";
                echo "        )\n";
            }
        }
        echo "    )\n";
        echo ")\n";
        echo "</pre>";

    } catch (Exception $e) {
        echo "<div class='error'>❌ AdminController simulation error: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 4: HTML Generation Test
    echo "<div class='debug-panel'>";
    echo "<h3>4. HTML Dropdown Generation</h3>";

    $roles = $roleModel->getAllRoles();

    echo "<div class='info'>Generating HTML dropdown with " . count($roles) . " roles:</div>";

    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
    echo "<strong>Actual HTML that should be rendered:</strong>";
    echo "<select class='form-control' style='width: 300px; padding: 8px; margin: 10px 0;'>";
    echo "<option value=''>Select Role</option>";

    if ($roles && count($roles) > 0) {
        foreach ($roles as $role) {
            echo "<option value='{$role->role_id}'>" . htmlspecialchars($role->role_name) . "</option>";
        }
        echo "</select>";
        echo "<div class='success'>✅ Dropdown generated successfully</div>";
    } else {
        echo "</select>";
        echo "<div class='error'>❌ No roles available for dropdown</div>";
    }
    echo "</div>";
    echo "</div>";

    // Test 5: Check actual admin/users endpoint
    echo "<div class='debug-panel'>";
    echo "<h3>5. Check Admin Users Page Access</h3>";

    if (isset($_SESSION['user_id'])) {
        echo "<div class='info'>Session active - testing admin access</div>";

        // Check if we can access the admin controller
        try {
            $adminController = new AdminController();
            echo "<div class='success'>✅ AdminController instantiated successfully</div>";

            // Try to get the users data
            ob_start();
            $adminController->users();
            $output = ob_get_clean();

            if (strlen($output) > 0) {
                echo "<div class='success'>✅ AdminController users() method executed</div>";
                echo "<div class='info'>Output length: " . strlen($output) . " characters</div>";
            } else {
                echo "<div class='warning'>⚠️ AdminController users() method returned no output</div>";
            }

        } catch (Exception $e) {
            echo "<div class='error'>❌ AdminController error: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='warning'>⚠️ No session - cannot test admin access</div>";
    }
    echo "</div>";

    // Test 6: Browser test link
    echo "<div class='debug-panel'>";
    echo "<h3>6. Direct Links for Testing</h3>";
    echo "<p><a href='admin/users' target='_blank' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔗 Open Admin Users Page</a></p>";
    echo "<p><strong>Instructions:</strong></p>";
    echo "<ol>";
    echo "<li>Click the link above to open the admin users page</li>";
    echo "<li>Click 'Add New User' button</li>";
    echo "<li>Check if the Role dropdown has options</li>";
    echo "<li>If still empty, there's an issue with the view file or data passing</li>";
    echo "</ol>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Critical Error</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Debug completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>