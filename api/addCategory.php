<?php
// Simple API endpoint for adding categories
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
        $categoryName = trim($_POST['category_name'] ?? '');

        if (empty($categoryName)) {
            http_response_code(400);
            echo json_encode(['error' => 'Please enter category name']);
            exit;
        }

        $database = new Database();

        // Check if category already exists
        $database->query('SELECT category_id FROM categories WHERE category_name = :category_name');
        $database->bind(':category_name', $categoryName);
        $existing = $database->single();

        if ($existing) {
            http_response_code(400);
            echo json_encode(['error' => 'Category name already exists']);
            exit;
        }

        // Add new category
        $database->query('INSERT INTO categories (category_name) VALUES (:category_name)');
        $database->bind(':category_name', $categoryName);

        if ($database->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add category']);
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