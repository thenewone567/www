<?php
require_once '../app/config.php';
require_once '../app/Database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->getDbh();

    echo json_encode(["message" => "Starting zero price fix"]) . "\n";

    // Fix 1: Update products with zero purchase_price
    echo json_encode(["action" => "Fixing zero base purchase prices"]) . "\n";

    $stmt = $pdo->prepare("
        UPDATE products 
        SET purchase_price = CASE 
            WHEN selling_price > 0 THEN selling_price * 0.6
            ELSE 10.00
        END
        WHERE purchase_price = 0 OR purchase_price IS NULL
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo json_encode(["success" => "Updated $updated products with realistic base purchase prices"]) . "\n";

    // Fix 2: Update product_suppliers with zero purchase_price
    echo json_encode(["action" => "Fixing zero supplier prices"]) . "\n";

    $stmt = $pdo->prepare("
        UPDATE product_suppliers ps
        JOIN products p ON ps.product_id = p.product_id
        SET ps.purchase_price = CASE 
            WHEN p.selling_price > 0 THEN p.selling_price * 0.65
            WHEN p.purchase_price > 0 THEN p.purchase_price * 1.05
            ELSE 12.00
        END
        WHERE ps.purchase_price = 0 OR ps.purchase_price IS NULL
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo json_encode(["success" => "Updated $updated supplier links with realistic prices"]) . "\n";

    // Fix 3: Update current_average_cost for products that still show 0
    echo json_encode(["action" => "Recalculating average costs"]) . "\n";

    $stmt = $pdo->prepare("
        UPDATE products 
        SET current_average_cost = CASE 
            WHEN purchase_price > 0 THEN purchase_price * 1.15
            WHEN selling_price > 0 THEN selling_price * 0.7
            ELSE 15.00
        END
        WHERE current_average_cost = 0 OR current_average_cost IS NULL
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo json_encode(["success" => "Updated $updated products with realistic average costs"]) . "\n";

    // Fix 4: Update any existing purchase items with zero costs
    echo json_encode(["action" => "Fixing existing purchase order items"]) . "\n";

    $stmt = $pdo->prepare("
        UPDATE purchase_items pi
        JOIN products p ON pi.product_id = p.product_id
        SET pi.unit_cost = CASE 
            WHEN p.purchase_price > 0 THEN p.purchase_price
            WHEN p.selling_price > 0 THEN p.selling_price * 0.6
            ELSE 10.00
        END,
        pi.total_cost = pi.quantity * CASE 
            WHEN p.purchase_price > 0 THEN p.purchase_price
            WHEN p.selling_price > 0 THEN p.selling_price * 0.6
            ELSE 10.00
        END
        WHERE pi.unit_cost = 0 OR pi.unit_cost IS NULL
    ");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo json_encode(["success" => "Updated $updated purchase items with realistic costs"]) . "\n";

    echo json_encode([
        "success" => true,
        "message" => "Zero price fix completed successfully",
        "note" => "All products now have realistic purchase prices, supplier prices, and average costs"
    ]) . "\n";

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]) . "\n";
}
?>