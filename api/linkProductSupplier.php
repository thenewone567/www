<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include the database configuration
require_once '../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON data');
    }

    $productId = $input['product_id'] ?? null;
    $supplierId = $input['supplier_id'] ?? null;
    $price = isset($input['price']) ? $input['price'] : null;
    $qualityRating = $input['quality_rating'] ?? null;
    $notes = $input['notes'] ?? '';

    // Validate required fields
    if (!$productId || !$supplierId) {
        throw new Exception('Product ID and Supplier ID are required');
    }

    $database = new Database();

    // Check if link already exists
    $database->query('
        SELECT ps_id FROM product_suppliers 
        WHERE product_id = :product_id AND supplier_id = :supplier_id
    ');
    $database->bind(':product_id', $productId);
    $database->bind(':supplier_id', $supplierId);
    $database->execute();

    if ($database->rowCount() > 0) {
        // Update existing link
        $database->query('
            UPDATE product_suppliers 
            SET purchase_price = :price, quality_rating = :quality_rating, notes = :notes, updated_at = NOW()
            WHERE product_id = :product_id AND supplier_id = :supplier_id
        ');
    } else {
        // Create new link
        $database->query('
            INSERT INTO product_suppliers (product_id, supplier_id, purchase_price, quality_rating, notes, created_at, updated_at)
            VALUES (:product_id, :supplier_id, :price, :quality_rating, :notes, NOW(), NOW())
        ');
    }

    // Validate price: require numeric positive value
    if ($price === null || $price === '' || !is_numeric($price) || floatval($price) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Valid price is required and must be greater than 0'
        ]);
        exit;
    }

    $price = floatval($price);

    $database->bind(':product_id', $productId);
    $database->bind(':supplier_id', $supplierId);
    $database->bind(':price', $price);
    $database->bind(':quality_rating', $qualityRating ?: null);
    $database->bind(':notes', $notes);

    $database->execute();

    // Get product and supplier names for response
    $database->query('SELECT product_name FROM products WHERE product_id = :product_id');
    $database->bind(':product_id', $productId);
    $database->execute();
    $product = $database->single();

    $database->query('SELECT supplier_name FROM suppliers WHERE supplier_id = :supplier_id');
    $database->bind(':supplier_id', $supplierId);
    $database->execute();
    $supplier = $database->single();

    echo json_encode([
        'success' => true,
        'message' => 'Successfully linked ' . ($product->product_name ?? 'product') . ' with ' . ($supplier->supplier_name ?? 'supplier'),
        'link_id' => $database->lastInsertId() ?: 'existing'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>