<?php
require_once 'config/database.php';

try {
    // Connect and show current database and tables
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Show all databases
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available databases:\n";
    foreach ($databases as $db) {
        echo "- " . $db . "\n";
    }
    
    // Select our database
    $pdo->exec("USE " . DB_NAME);
    echo "\nUsing database: " . DB_NAME . "\n";
    
    // Show tables in our database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in " . DB_NAME . ":\n";
    if (empty($tables)) {
        echo "No tables found!\n";
        
        // Import the database again
        echo "Importing database structure...\n";
        $sql = file_get_contents('database/migrations/database.sql');
        
        // Split and execute statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $successCount = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^(\/\*|--|\#|SET|START|COMMIT)/', $statement)) {
                try {
                    $pdo->exec($statement);
                    $successCount++;
                } catch (PDOException $e) {
                    if (!strpos($e->getMessage(), 'already exists')) {
                        echo "Error executing: " . substr($statement, 0, 50) . "...\n";
                        echo "Error: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "Executed " . $successCount . " statements\n";
        
        // Check tables again
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables after import:\n";
    }
    
    foreach ($tables as $table) {
        echo "- " . $table . "\n";
    }
    
    // Specifically check for sales table
    $stmt = $pdo->query("SHOW TABLES LIKE 'sales'");
    $salesTable = $stmt->fetch();
    
    if ($salesTable) {
        echo "\nSales table found! Structure:\n";
        $stmt = $pdo->query("DESCRIBE sales");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    } else {
        echo "\nSales table still not found after import!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
