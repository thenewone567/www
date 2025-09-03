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

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
        exit;
    }

    $productId = $input['product_id'] ?? null;
    $supplierId = $input['supplier_id'] ?? null;
    $price = $input['price'] ?? null;
    $qualityRating = $input['quality_rating'] ?? null;
    $notes = $input['notes'] ?? '';

    // Validate required fields
    if (empty($productId) || empty($supplierId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Product ID and Supplier ID are required']);
        exit;
    }

    // Validate price: require numeric positive value
    if ($price === null || $price === '' || !is_numeric($price) || floatval($price) <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid price is required and must be greater than 0']);
        exit;
    }

    $price = floatval($price);

    $database = new Database();

    // Check if link already exists
    $database->query('SELECT ps_id FROM product_suppliers WHERE product_id = ? AND supplier_id = ?');
    $database->bind(1, $productId);
    $database->bind(2, $supplierId);
    $database->execute();

    if ($database->rowCount() > 0) {
        // Update existing link
        $database->query('UPDATE product_suppliers SET purchase_price = ?, quality_rating = ?, notes = ?, updated_at = NOW() WHERE product_id = ? AND supplier_id = ?');
        $isInsert = false;
    } else {
        // Create new link
        $database->query('INSERT INTO product_suppliers (product_id, supplier_id, purchase_price, quality_rating, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
        $isInsert = true;
    }

    $database->bind(1, $productId);
    $database->bind(2, $supplierId);
    $database->bind(3, $price);
    $database->bind(4, $qualityRating ?: null);
    $database->bind(5, $notes);

    // For UPDATE, we need different parameter order
    if (!$isInsert) {
        // Re-bind for UPDATE: SET purchase_price = ?, quality_rating = ?, notes = ? WHERE product_id = ? AND supplier_id = ?
        $database->bind(1, $price);
        $database->bind(2, $qualityRating ?: null);
        $database->bind(3, $notes);
        $database->bind(4, $productId);
        $database->bind(5, $supplierId);
    }

    $exec = $database->execute();
    if (!$exec) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database operation failed', 'db_error' => $database->getLastError()]);
        exit;
    }

    // Fetch product and supplier names for response
    $database->query('SELECT product_name FROM products WHERE product_id = ?');
    $database->bind(1, $productId);
    $database->execute();
    $product = $database->single();

    $database->query('SELECT supplier_name FROM suppliers WHERE supplier_id = ?');
    $database->bind(1, $supplierId);
    $database->execute();
    $supplier = $database->single();

    $linkId = null;
    if ($isInsert) {
        // Try to get last insert id from database wrapper if available
        if (method_exists($database, 'lastInsertId')) {
            $linkId = $database->lastInsertId();
        } else {
            // Fall back: query for created record
            $database->query('SELECT ps_id FROM product_suppliers WHERE product_id = ? AND supplier_id = ? ORDER BY created_at DESC LIMIT 1');
            $database->bind(1, $productId);
            $database->bind(2, $supplierId);
            $database->execute();
            $r = $database->single();
            $linkId = $r->ps_id ?? null;
        }
    } else {
        // For update, return existing ps_id
        $database->query('SELECT ps_id FROM product_suppliers WHERE product_id = ? AND supplier_id = ? LIMIT 1');
        $database->bind(1, $productId);
        $database->bind(2, $supplierId);
        $database->execute();
        $r = $database->single();
        $linkId = $r->ps_id ?? 'existing';
    }

    echo json_encode([
        'success' => true,
        'message' => 'Successfully linked ' . ($product->product_name ?? 'product') . ' with ' . ($supplier->supplier_name ?? 'supplier'),
        'link_id' => $linkId
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
