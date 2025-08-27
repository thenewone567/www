<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include the database configuration
require_once '../app/config.php';
require_once '../app/Database.php';

try {
    $database = new Database();
    $db = $database->getDbh();

    // Get all units
    $sql = "SELECT unit_id, unit_name FROM units ORDER BY unit_name ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    echo json_encode($units);

} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'units' => []
    ]);
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred: ' . $e->getMessage(),
        'units' => []
    ]);
}
?>