<?php
require_once 'bootstrap.php';

try {
    $db = new Database();
    
    echo "=== ADDING DOCK AND RECEIVING AREA SUPPORT ===\n";
    
    // 1. Add columns to purchases table
    echo "1. Adding columns to purchases table...\n";
    
    $alterQueries = [
        "ALTER TABLE purchases ADD COLUMN dock_location_id INT NULL AFTER received_at",
        "ALTER TABLE purchases ADD COLUMN receiving_area_id INT NULL AFTER dock_location_id",
        "ALTER TABLE purchases ADD COLUMN dock_assignment_notes VARCHAR(500) NULL AFTER receiving_area_id"
    ];
    
    foreach ($alterQueries as $query) {
        try {
            $db->query($query);
            $db->execute();
            echo "   ✓ Executed: $query\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "   ⚠ Column already exists: $query\n";
            } else {
                echo "   ✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 2. Create dock locations
    echo "\n2. Creating dock locations...\n";
    
    $dockLocations = [
        ['DOCK-1', 'Dock 1', 'Loading Dock 1 - Main entrance', 'dock'],
        ['DOCK-2', 'Dock 2', 'Loading Dock 2 - Side entrance', 'dock'],
        ['DOCK-3', 'Dock 3', 'Loading Dock 3 - Rear entrance', 'dock']
    ];
    
    foreach ($dockLocations as $dock) {
        $db->query("
            INSERT INTO locations (location_code, location_name, notes, location_type, zone, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'RECEIVING', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                location_name = VALUES(location_name),
                notes = VALUES(notes),
                updated_at = NOW()
        ");
        $db->bind(1, $dock[0]);
        $db->bind(2, $dock[1]);
        $db->bind(3, $dock[2]);
        $db->bind(4, $dock[3]);
        $db->execute();
        echo "   ✓ Created/Updated dock: {$dock[1]}\n";
    }
    
    // 3. Create receiving area locations
    echo "\n3. Creating receiving area locations...\n";
    
    $receivingAreas = [
        ['RA-1', 'Receiving Area 1', 'Primary receiving and staging area', 'receiving'],
        ['RA-2', 'Receiving Area 2', 'Secondary receiving and staging area', 'receiving'],
        ['RA-3', 'Receiving Area 3', 'Overflow receiving and staging area', 'receiving']
    ];
    
    foreach ($receivingAreas as $area) {
        $db->query("
            INSERT INTO locations (location_code, location_name, notes, location_type, zone, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'RECEIVING', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                location_name = VALUES(location_name),
                notes = VALUES(notes),
                updated_at = NOW()
        ");
        $db->bind(1, $area[0]);
        $db->bind(2, $area[1]);
        $db->bind(3, $area[2]);
        $db->bind(4, $area[3]);
        $db->execute();
        echo "   ✓ Created/Updated receiving area: {$area[1]}\n";
    }
    
    // 4. Add foreign key constraints
    echo "\n4. Adding foreign key constraints...\n";
    
    $fkQueries = [
        "ALTER TABLE purchases ADD CONSTRAINT fk_dock_location FOREIGN KEY (dock_location_id) REFERENCES locations(location_id) ON DELETE SET NULL",
        "ALTER TABLE purchases ADD CONSTRAINT fk_receiving_area FOREIGN KEY (receiving_area_id) REFERENCES locations(location_id) ON DELETE SET NULL"
    ];
    
    foreach ($fkQueries as $query) {
        try {
            $db->query($query);
            $db->execute();
            echo "   ✓ Added foreign key constraint\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "   ⚠ Foreign key constraint already exists\n";
            } else {
                echo "   ✗ FK Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 5. Verify the setup
    echo "\n5. Verifying setup...\n";
    
    $db->query("SELECT location_id, location_code, location_name, location_type FROM locations WHERE location_type IN ('dock', 'receiving') ORDER BY location_type, location_code");
    $db->execute();
    $locations = $db->resultSet();
    
    echo "   Available dock and receiving locations:\n";
    foreach ($locations as $loc) {
        echo "   - {$loc->location_code}: {$loc->location_name} ({$loc->location_type})\n";
    }
    
    echo "\n✅ Database setup complete!\n";
    echo "Now updating the Quick Receive interface...\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
