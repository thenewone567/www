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

    // Get search parameter
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50; // Default limit of 50 products

    // Build the SQL query
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