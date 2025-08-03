<?php
require_once 'app/config.php';
require_once 'app/Database.php';

$db = new Database();

echo "Checking all tables in database...\n";

try {
    $db->query("SHOW TABLES");
    $db->execute();
    $tables = $db->resultSet();
    
    foreach ($tables as $table) {
        $tableName = current((array)$table);
        echo "Table: $tableName\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
