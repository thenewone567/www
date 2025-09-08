<?php
require 'bootstrap.php';

$db = new Database();
$db->query("SELECT location_code, location_name FROM locations WHERE location_type = 'receiving' AND is_active = 1 ORDER BY location_code");
$db->execute();
$locations = $db->resultSet();

echo "Receiving Area Locations:\n";
echo "========================\n";

if (count($locations) > 0) {
    foreach ($locations as $loc) {
        echo "- {$loc->location_code}: {$loc->location_name}\n";
    }
    echo "\nTotal: " . count($locations) . " receiving area locations available\n";
} else {
    echo "❌ No receiving area locations found!\n";
    echo "The system needs receiving area locations for this feature to work.\n";
}
?>
