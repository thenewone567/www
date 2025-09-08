<?php
require_once 'bootstrap.php';

echo "Debugging Database class connection...\n";

try {
    $db = new Database();
    
    // Check which database we're connected to
    $db->query('SELECT DATABASE() as current_db');
    $result = $db->single();
    
    if ($result) {
        echo "Connected to database: " . $result->current_db . "\n";
    }
    
    // Check if we can see any tables
    $db->query('SHOW TABLES');
    $tables = $db->resultSet();
    
    echo "Tables visible to Database class:\n";
    if ($tables) {
        foreach($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- " . $tableName . "\n";
        }
    } else {
        echo "No tables found\n";
    }
    
    // Try to check if companies table exists
    $db->query("SHOW TABLES LIKE 'companies'");
    $companiesExists = $db->single();
    
    if ($companiesExists) {
        echo "\nCompanies table is visible\n";
        
        // Try a simple count
        $db->query('SELECT COUNT(*) as total FROM companies');
        $count = $db->single();
        
        if ($count) {
            echo "Companies count: " . $count->total . "\n";
        } else {
            echo "Failed to get count\n";
        }
        
    } else {
        echo "\nCompanies table is NOT visible to Database class\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
