<?php
// Include the database connection
require_once 'app/config.php';
require_once 'app/Database.php';

$db = new Database();

echo "<h1>Database Schema Detection</h1>";

// Check roles table structure
echo "<h2>Roles Table Structure:</h2>";
try {
    $db->query('DESCRIBE roles');
    $roleStructure = $db->resultSet();
    echo "<pre>";
    print_r($roleStructure);
    echo "</pre>";

    // Based on the structure, determine which schema is used
    $hasIdColumn = false;
    $hasRoleIdColumn = false;
    $hasNameColumn = false;
    $hasRoleNameColumn = false;

    foreach ($roleStructure as $column) {
        if ($column->Field === 'id')
            $hasIdColumn = true;
        if ($column->Field === 'role_id')
            $hasRoleIdColumn = true;
        if ($column->Field === 'name')
            $hasNameColumn = true;
        if ($column->Field === 'role_name')
            $hasRoleNameColumn = true;
    }

    echo "<h3>Schema Detection Results:</h3>";
    echo "Has 'id' column: " . ($hasIdColumn ? 'YES' : 'NO') . "<br>";
    echo "Has 'role_id' column: " . ($hasRoleIdColumn ? 'YES' : 'NO') . "<br>";
    echo "Has 'name' column: " . ($hasNameColumn ? 'YES' : 'NO') . "<br>";
    echo "Has 'role_name' column: " . ($hasRoleNameColumn ? 'YES' : 'NO') . "<br>";

    if ($hasIdColumn && $hasNameColumn) {
        echo "<strong>Using admin_panel_setup.sql schema (id, name)</strong><br>";
        $schema = 'admin_panel';
    } elseif ($hasRoleIdColumn && $hasRoleNameColumn) {
        echo "<strong>Using add_enhancement_tables.sql schema (role_id, role_name)</strong><br>";
        $schema = 'enhancement';
    } else {
        echo "<strong>Mixed or unknown schema detected!</strong><br>";
        $schema = 'mixed';
    }

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

// Show current roles data
echo "<h2>Current Roles Data:</h2>";
try {
    $db->query('SELECT * FROM roles ORDER BY ' . ($hasIdColumn ? 'id' : 'role_id'));
    $roles = $db->resultSet();
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error fetching roles: " . $e->getMessage();
}

// Show users data
echo "<h2>Current Users Data:</h2>";
try {
    $db->query('SELECT user_id, username, role_id FROM users ORDER BY user_id');
    $users = $db->resultSet();
    echo "<pre>";
    print_r($users);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error fetching users: " . $e->getMessage();
}

// Test the correct JOIN query based on schema
echo "<h2>Testing Correct JOIN Query:</h2>";
try {
    if ($hasIdColumn && $hasNameColumn) {
        // admin_panel schema
        $db->query('
            SELECT u.*, r.name as role_name, r.permissions 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.username = :username
        ');
        $db->bind(':username', 'sukhdev');
    } else {
        // enhancement schema  
        $db->query('
            SELECT u.*, r.role_name, r.permissions 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            WHERE u.username = :username
        ');
        $db->bind(':username', 'sukhdev');
    }

    $user = $db->single();
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error testing JOIN query: " . $e->getMessage();
}

?>