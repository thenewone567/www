<?php
require_once 'bootstrap.php';

echo "=== PURCHASE RECEIVING BULK LOCATIONS VERIFICATION ===\n\n";

try {
    $db = new Database();

    // Test the exact same query used by the Purchase model
    $db->query("
        SELECT location_id, location_name, rack, shelf 
        FROM warehouse_locations 
        WHERE location_name LIKE 'B-%' 
        ORDER BY location_name
    ");

    $bulkLocations = $db->resultSet();

    echo "Bulk locations found for purchase receiving:\n";
    echo "=" . str_repeat("=", 50) . "\n";

    if (empty($bulkLocations)) {
        echo "❌ NO BULK LOCATIONS FOUND!\n";
        echo "The purchase receiving page will show 'No bulk location found'\n";
    } else {
        echo "✅ Found " . count($bulkLocations) . " bulk locations:\n\n";

        foreach ($bulkLocations as $location) {
            echo "ID: {$location->location_id}\n";
            echo "Name: {$location->location_name}\n";
            echo "Rack: {$location->rack}\n";
            echo "Shelf: {$location->shelf}\n";
            echo "-" . str_repeat("-", 30) . "\n";
        }

        echo "\n✅ Purchase receiving should now work properly!\n";
        echo "Users can select from these bulk locations when receiving items.\n";
    }

    // Also show regular locations count
    $db->query("SELECT COUNT(*) as count FROM warehouse_locations WHERE location_name NOT LIKE 'B-%'");
    $regularResult = $db->single();
    $regularCount = $regularResult ? $regularResult->count : 0;

    echo "\nAdditional info:\n";
    echo "Regular storage locations: {$regularCount}\n";
    echo "Total warehouse locations: " . (count($bulkLocations) + $regularCount) . "\n";

    echo "\nNext steps:\n";
    echo "1. Visit: http://localhost/purchases/receive_items/8\n";
    echo "2. You should see bulk locations in the dropdown\n";
    echo "3. Select a bulk location and receive items\n";
    echo "4. Check inventory management for received items\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>