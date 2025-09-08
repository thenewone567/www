<?php
/**
 * Debug API to check product lookup issues
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $barcode = $input['barcode'] ?? $_GET['barcode'] ?? '';

    require_once __DIR__ . '/../bootstrap.php';
    $db = new Database();

    $debug = [
        'input_barcode' => $barcode,
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'raw_input' => file_get_contents('php://input'),
        'parsed_input' => $input
    ];

    if (!empty($barcode)) {
        // Check if product exists with exact match
        $db->query("SELECT product_id, product_name, sku, barcode FROM products WHERE sku = ? OR barcode = ?");
        $db->bind(1, $barcode);
        $db->bind(2, $barcode);
        $db->execute();
        $product = $db->single();

        $debug['product_found'] = $product ? true : false;
        if ($product) {
            $debug['product'] = $product;
        }

        // Check what products exist with similar SKUs
        $db->query("SELECT product_id, product_name, sku, barcode FROM products WHERE sku LIKE ? OR barcode LIKE ? LIMIT 5");
        $db->bind(1, '%' . $barcode . '%');
        $db->bind(2, '%' . $barcode . '%');
        $db->execute();
        $similarProducts = $db->resultSet();
        $debug['similar_products'] = $similarProducts;

        // Check sample products
        $db->query("SELECT product_id, product_name, sku, barcode FROM products LIMIT 5");
        $db->execute();
        $sampleProducts = $db->resultSet();
        $debug['sample_products'] = $sampleProducts;
    }

    echo json_encode([
        'success' => true,
        'debug' => $debug
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $debug ?? []
    ]);
}
?>