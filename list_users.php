<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    // Show all users with their details
    $db->query("SELECT user_id, name, email, is_active FROM users ORDER BY user_id");
    $db->execute();
    $allUsers = $db->resultSet();

    echo "All users in database:\n";
    echo "ID | Name | Email | Active\n";
    echo "---|------|-------|-------\n";

    foreach ($allUsers as $u) {
        echo "{$u->user_id} | {$u->name} | {$u->email} | {$u->is_active}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>