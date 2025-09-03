<?php
// Disable HTML error output to ensure clean JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once '../app/config.php';
require_once '../app/helpers.php';

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Add debug logging
    error_log("updateProductPrice.php called at " . date('Y-m-d H:i:s'));
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Raw input: " . file_get_contents('php://input'));

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        error_log("Failed to decode JSON input");
        throw new Exception('Invalid JSON input');
    }

    error_log("Decoded input: " . print_r($input, true));

    // Validate required fields
    if (!isset($input['product_id']) || !isset($input['price'])) {
        throw new Exception('Product ID and price are required');
    }

    $productId = (int) $input['product_id'];
    $price = (float) $input['price'];

    // Validate inputs
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }

    if ($price < 0) {
        throw new Exception('Price cannot be negative');
    }

    // Load the Product model
    require_once '../app/models/Product.php';
    $productModel = new Product();

    // Update the product price
    error_log("Attempting to update product $productId with price $price");
    $result = $productModel->updateProductPrice($productId, $price);
    error_log("Update result: " . ($result ? 'SUCCESS' : 'FAILED'));

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Product price updated successfully',
            'product_id' => $productId,
            'new_price' => $price
        ]);
    } else {
        throw new Exception('Failed to update product price');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>