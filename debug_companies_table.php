<?php
try {
    require_once 'bootstrap.php';
    $db = new Database();
    
    // Check if companies table exists
    $db->query('SHOW TABLES LIKE "companies"');
    $tableExists = $db->single();
    
    if ($tableExists) {
        echo "Companies table exists\n";
        
        // Get table structure
        $db->query('DESCRIBE companies');
        $structure = $db->resultSet();
        
        echo "Table structure:\n";
        foreach($structure as $row) {
            echo $row->Field . " - " . $row->Type . " - " . $row->Null . " - " . $row->Key . " - " . $row->Default . "\n";
        }
        
        // Check if any company record exists
        $db->query('SELECT COUNT(*) as count FROM companies');
        $count = $db->single();
        echo "\nNumber of companies: " . $count->count . "\n";
        
        // Try to get company 1
        $db->query('SELECT * FROM companies WHERE company_id = 1 LIMIT 1');
        $company = $db->single();
        if ($company) {
            echo "\nCompany 1 exists:\n";
            print_r($company);
        } else {
            echo "\nNo company with ID 1 found\n";
        }
        
    } else {
        echo "Companies table does not exist!\n";
        echo "Available tables:\n";
        $db->query('SHOW TABLES');
        $tables = $db->resultSet();
        foreach($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- " . $tableName . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
