<?php
require_once 'bootstrap.php';

echo "Testing database connection...\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Direct PDO connection successful!\n";
    
    // Check if companies table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'companies'");
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Companies table exists!\n";
        
        // Get structure
        $stmt = $pdo->query("DESCRIBE companies");
        $structure = $stmt->fetchAll();
        
        echo "Table structure:\n";
        foreach($structure as $row) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
        
        // Get count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM companies");
        $count = $stmt->fetch();
        echo "\nNumber of records: " . $count['count'] . "\n";
        
    } else {
        echo "Companies table does NOT exist!\n";
        
        echo "Available tables:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        foreach($tables as $table) {
            $tableName = array_values($table)[0];
            echo "- " . $tableName . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
?>
