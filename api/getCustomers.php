<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include the database configuration
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

try {
    // Load Customer model
    require_once '../app/models/Customer.php';
    $customerModel = new Customer();

    // Get all customers
    $customers = $customerModel->getCustomers();

    echo json_encode([
        'success' => true,
        'data' => $customers,
        'count' => count($customers)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>