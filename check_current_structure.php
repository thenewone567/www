<?php
require_once 'bootstrap.php';

try {
    $db = new Database();
    
    echo "=== CURRENT DATABASE STRUCTURE ===\n";
    
    // Check purchases table structure
    echo "1. PURCHASES TABLE:\n";
    $db->query('DESCRIBE purchases');
    $db->execute();
    $columns = $db->resultSet();
    
    foreach($columns as $col) {
        echo "   - " . $col->Field . " (" . $col->Type . ")\n";
    }
    
    // Check if locations table exists
    echo "\n2. CHECKING LOCATIONS TABLE:\n";
    $db->query("SHOW TABLES LIKE 'locations'");
    $db->execute();
    $locationsExists = $db->single();
    
    if ($locationsExists) {
        echo "   ✓ Locations table exists\n";
        $db->query('DESCRIBE locations');
        $db->execute();
        $locColumns = $db->resultSet();
        
        foreach($locColumns as $col) {
            echo "   - " . $col->Field . " (" . $col->Type . ")\n";
        }
        
        // Check current data
        echo "\n   Current location data:\n";
        $db->query('SELECT * FROM locations LIMIT 10');
        $db->execute();
        $locations = $db->resultSet();
        
        foreach($locations as $loc) {
            echo "   - " . print_r($loc, true) . "\n";
        }
    } else {
        echo "   ✗ Locations table does not exist\n";
    }
    
    // Check for any dock-related columns in purchases
    echo "\n3. CHECKING FOR DOCK-RELATED COLUMNS IN PURCHASES:\n";
    $dockColumns = array_filter($columns, function($col) {
        return stripos($col->Field, 'dock') !== false || stripos($col->Field, 'location') !== false;
    });
    
    if (empty($dockColumns)) {
        echo "   ✗ No dock-related columns found in purchases table\n";
    } else {
        foreach($dockColumns as $col) {
            echo "   ✓ Found: " . $col->Field . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
