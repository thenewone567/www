<?php
require_once 'bootstrap.php';

echo "=== WAREHOUSE LOCATIONS CHECK ===\n\n";

try {
    $db = new Database();

    // Check current locations
    $db->query("SELECT location_id, location_name, rack, shelf FROM warehouse_locations ORDER BY location_name");
    $locations = $db->resultSet();

    echo "Current locations in database: " . count($locations) . "\n";
    echo "----------------------------------------\n";

    $bulkCount = 0;
    $regularCount = 0;

    foreach ($locations as $loc) {
        echo "ID: {$loc->location_id} | Name: {$loc->location_name} | Rack: {$loc->rack} | Shelf: {$loc->shelf}\n";

        if (strpos($loc->location_name, 'B-') === 0) {
            $bulkCount++;
        } else {
            $regularCount++;
        }
    }

    echo "\n=== SUMMARY ===\n";
    echo "Total locations: " . count($locations) . "\n";
    echo "Regular locations: {$regularCount}\n";
    echo "Bulk locations (B- prefix): {$bulkCount}\n";

    if ($bulkCount === 0) {
        echo "\n❌ NO BULK LOCATIONS FOUND!\n";
        echo "This is why the purchase receiving page shows 'No bulk location found'\n";
        echo "\nAdding essential bulk locations...\n";

        $bulkLocations = [
            ['B-RECV-01', 'RECV', '01'],
            ['B-RECV-02', 'RECV', '02'],
            ['B-TEMP-01', 'TEMP', '01'],
            ['B-BULK-01', 'BULK', '01']
        ];

        foreach ($bulkLocations as $bulk) {
            $db->query("INSERT INTO warehouse_locations (location_name, rack, shelf) VALUES (?, ?, ?)");
            $db->bind(1, $bulk[0]);
            $db->bind(2, $bulk[1]);
            $db->bind(3, $bulk[2]);
            $db->execute();
            echo "✅ Added: {$bulk[0]}\n";
        }

        echo "\n✅ Bulk locations added successfully!\n";
        echo "You can now use the purchase receiving functionality.\n";
    } else {
        echo "\n✅ Bulk locations available for purchase receiving!\n";
        foreach ($locations as $loc) {
            if (strpos($loc->location_name, 'B-') === 0) {
                echo "  - {$loc->location_name} (Rack: {$loc->rack})\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>