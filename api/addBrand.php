<?php
// Simple API endpoint for adding brands
require_once '../bootstrap.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $brandName = trim($_POST['brand_name'] ?? '');

        if (empty($brandName)) {
            http_response_code(400);
            echo json_encode(['error' => 'Please enter brand name']);
            exit;
        }

        $database = new Database();

        // Check if brand already exists
        $database->query('SELECT brand_id FROM brands WHERE brand_name = :brand_name');
        $database->bind(':brand_name', $brandName);
        $existing = $database->single();

        if ($existing) {
            http_response_code(400);
            echo json_encode(['error' => 'Brand name already exists']);
            exit;
        }

        // Add new brand
        $database->query('INSERT INTO brands (brand_name) VALUES (:brand_name)');
        $database->bind(':brand_name', $brandName);

        if ($database->execute()) {
            echo json_encode(['success' => true, 'message' => 'Brand added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add brand']);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>