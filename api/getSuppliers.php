<?php
// Simple API endpoint for getting suppliers
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

    // Get search parameter
    $search = $_GET['search'] ?? '';

    if (!empty($search)) {
        // Search suppliers by name, contact person, phone, or email
        $database->query('
            SELECT supplier_id, supplier_name, contact_person, phone, email, address
            FROM suppliers 
            WHERE deleted_at IS NULL
            AND (
                supplier_name LIKE :search 
                OR contact_person LIKE :search 
                OR phone LIKE :search 
                OR email LIKE :search
            )
            ORDER BY supplier_name ASC
            LIMIT 50
        ');
        $database->bind(':search', '%' . $search . '%');
    } else {
        // Get all suppliers
        $database->query('
            SELECT supplier_id, supplier_name, contact_person, phone, email, address
            FROM suppliers 
            WHERE deleted_at IS NULL
            ORDER BY supplier_name ASC
            LIMIT 50
        ');
    }

    $database->execute();
    $suppliers = $database->resultSet();

    echo json_encode([
        'success' => true,
        'suppliers' => $suppliers
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch suppliers: ' . $e->getMessage()
    ]);
}
?>