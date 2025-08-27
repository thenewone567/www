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

    // Check if requesting single product by ID
    $productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($productId > 0) {
        // Single product query with full details
        $sql = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.sku,
                    p.barcode,
                    p.supplier_code,
                    p.category_id,
                    p.brand_id,
                    p.unit_id,
                    p.product_type,
                    p.has_expiry,
                    p.expiry_months,
                    p.min_Inventory_level,
                    p.max_Inventory_level,
                    p.reorder_level,
                    p.purchase_price,
                    p.selling_price,
                    p.profit_margin,
                    p.weight,
                    p.dimensions,
                    p.warranty_period,
                    p.image_path,
                    p.status,
                    c.category_name,
                    b.brand_name,
                    u.unit_name,
                    COALESCE(SUM(inv.quantity), 0) AS current_inventory
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                LEFT JOIN inventory inv ON p.product_id = inv.product_id
                WHERE p.product_id = :product_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Product not found'
            ]);
        }
        return;
    }

    // Get search parameter for multiple products
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50; // Default limit of 50 products

    // Build the SQL query for multiple products
    $sql = "SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                p.barcode,
                p.selling_price,
                COALESCE(SUM(inv.quantity), 0) AS current_inventory,
                p.status,
                c.category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN inventory inv ON p.product_id = inv.product_id
            WHERE p.status = 'active'";

    $params = [];

    // Add search conditions if search term is provided
    if (!empty($search)) {
        $sql .= " AND (
            p.product_name LIKE :search1 OR 
            p.sku LIKE :search2 OR 
            p.barcode LIKE :search3 OR
            c.category_name LIKE :search4
        )";
        $searchTerm = '%' . $search . '%';
        $params[':search1'] = $searchTerm;
        $params[':search2'] = $searchTerm;
        $params[':search3'] = $searchTerm;
        $params[':search4'] = $searchTerm;
    }

    // Add ordering, grouping and limit
    $sql .= " GROUP BY p.product_id, p.product_name, p.sku, p.barcode, p.selling_price, p.status, c.category_name 
              ORDER BY p.product_name ASC LIMIT :limit";
    $params[':limit'] = $limit;

    // Prepare and execute the query
    $stmt = $db->prepare($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
        if ($key === ':limit') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $response = [
        'success' => true,
        'products' => $products,
        'count' => count($products),
        'search_term' => $search
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage(),
        'products' => []
    ]);
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred: ' . $e->getMessage(),
        'products' => []
    ]);
}
?>