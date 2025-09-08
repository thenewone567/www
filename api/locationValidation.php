<?php
/**
 * Location Validation API for Putaway Scanner
 * Validates scanned storage locations and checks capacity
 */

// Turn off error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $locationCode = $input['location_code'] ?? $_GET['location_code'] ?? '';

    if (empty($locationCode)) {
        throw new Exception('Location code is required');
    }

    require_once __DIR__ . '/../bootstrap.php';
    $db = new Database();

    // Look up location by code
    $db->query("SELECT 
        location_id,
        location_code,
        location_name,
        location_type,
        capacity_cubic_feet,
        max_weight_kg,
        is_active,
        zone,
        aisle,
        shelf,
        bin
        FROM locations 
        WHERE location_code = ?");
    $db->bind(1, $locationCode);
    $db->execute();
    $location = $db->single();

    if (!$location) {
        throw new Exception('Location not found: ' . $locationCode);
    }

    if (!$location->is_active) {
        throw new Exception('Location is inactive: ' . $locationCode);
    }

    if ($location->location_type !== 'storage') {
        throw new Exception('Location is not a storage location: ' . $locationCode . ' (Type: ' . $location->location_type . ')');
    }

    // Check current inventory at this location
    $db->query("SELECT 
        SUM(quantity) as total_quantity,
        COUNT(DISTINCT product_id) as product_count
        FROM inventory 
        WHERE location_id = ?");
    $db->bind(1, $location->location_id);
    $db->execute();
    $currentInventory = $db->single();

    // Calculate capacity utilization
    $utilizationPercent = 0;
    $availableSpace = 'Unlimited';
    if ($location->capacity_cubic_feet) {
        $utilizationPercent = ($currentInventory->total_quantity / $location->capacity_cubic_feet) * 100;
        $availableSpace = $location->capacity_cubic_feet - $currentInventory->total_quantity;
    }

    $response = [
        'success' => true,
        'location' => [
            'id' => $location->location_id,
            'code' => $location->location_code,
            'name' => $location->location_name,
            'type' => $location->location_type,
            'zone' => $location->zone,
            'aisle' => $location->aisle,
            'shelf' => $location->shelf,
            'bin' => $location->bin
        ],
        'capacity' => [
            'total_cubic_feet' => $location->capacity_cubic_feet,
            'max_weight_kg' => $location->max_weight_kg,
            'current_quantity' => $currentInventory->total_quantity ?? 0,
            'product_count' => $currentInventory->product_count ?? 0,
            'utilization_percent' => round($utilizationPercent, 1),
            'available_space' => $availableSpace,
            'status' => $utilizationPercent > 90 ? 'nearly_full' :
                ($utilizationPercent > 70 ? 'getting_full' : 'available')
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>