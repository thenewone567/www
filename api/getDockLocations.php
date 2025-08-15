<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any HTML output from error handlers
ini_set('display_errors', 0);
ob_start();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Only GET method allowed');
    }

    // Initialize database
    $db = new Database();

    // Get dock locations
    $db->query("SELECT location_id, location_code, location_name FROM locations WHERE location_type = 'dock' AND is_active = 1 ORDER BY location_code");
    $db->execute();
    $docks = $db->resultSet();

    // Get receiving area locations
    $db->query("SELECT location_id, location_code, location_name FROM locations WHERE location_type = 'receiving' AND is_active = 1 ORDER BY location_code");
    $db->execute();
    $receivingAreas = $db->resultSet();

    // Clean any unwanted output before sending response
    ob_end_clean();

    echo json_encode([
        'success' => true,
        'data' => [
            'docks' => $docks,
            'receiving_areas' => $receivingAreas
        ]
    ]);

} catch (Exception $e) {
    // Clean any unwanted output before sending error response
    ob_end_clean();
    error_log("Error in getDockLocations.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>