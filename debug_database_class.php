<?php
require_once 'bootstrap.php';

echo "Debugging Company model...\n";

try {
    // Test Database class directly
    echo "Testing Database class...\n";
    $db = new Database();
    
    // Test basic query
    $db->query('SELECT * FROM companies WHERE company_id = 1');
    $result = $db->single();
    
    if ($result) {
        echo "Direct Database query works:\n";
        print_r($result);
    } else {
        echo "Direct Database query returns null\n";
    }
    
    // Test the exact query from Company model
    echo "\nTesting Company model query...\n";
    $db->query('SELECT * FROM companies WHERE company_id = :id LIMIT 1');
    $db->bind(':id', 1);
    $result2 = $db->single();
    
    if ($result2) {
        echo "Company model query works:\n";
        print_r($result2);
    } else {
        echo "Company model query returns null\n";
    }
    
    // Test insert to see if the record exists with correct ID
    echo "\nChecking all companies...\n";
    $db->query('SELECT * FROM companies');
    $all = $db->resultSet();
    
    echo "All companies:\n";
    foreach($all as $comp) {
        print_r($comp);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
