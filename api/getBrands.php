<?php
// Simple API endpoint for getting brands
require_once '../bootstrap.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    $database = new Database();
    $database->query('SELECT * FROM brands WHERE is_active = 1 ORDER BY brand_name ASC');
    $database->execute();
    $brands = $database->resultSet();

    echo json_encode($brands);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch brands: ' . $e->getMessage()]);
}
?>