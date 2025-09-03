<?php
require_once __DIR__ . '/../bootstrap.php';

echo "🔍 ANALYZING PRIMARY SUPPLIER vs AUTOMATED PURCHASING CONFLICTS\n";
echo "==============================================================\n\n";

// Connect to database
$db = new Database();

echo "1️⃣ CHECKING AUTOMATED BOT LOGIC:\n";
echo "--------------------------------\n";

// Check what the bot's findCheapestSupplier does vs primary supplier logic
echo "Bot's findCheapestSupplier query:\n";
echo "• Query: SELECT ... ORDER BY ps.purchase_price ASC LIMIT 1\n";
echo "• Logic: ALWAYS chooses cheapest price (ignores is_primary)\n\n";

echo "Bot's getAllSuppliersForProduct query:\n";
echo "• Query: SELECT ... ORDER BY ps.purchase_price ASC\n";
echo "• Logic: Orders by price, not by is_primary\n\n";

echo "2️⃣ CHECKING MANUAL ORDERING LOGIC:\n";
echo "----------------------------------\n";

// Check purchase order creation logic
$db->query("
    SELECT 
        COUNT(*) as total_links,
        SUM(CASE WHEN is_primary = 1 THEN 1 ELSE 0 END) as primary_links,
        COUNT(DISTINCT product_id) as products_with_links,
        SUM(CASE WHEN is_primary = 1 THEN 1 ELSE 0 END) as products_with_primary
    FROM product_suppliers 
    WHERE is_active = 1
");
$db->execute();
$stats = $db->single();

echo "Current supplier link statistics:\n";
echo "• Total active supplier links: {$stats->total_links}\n";
echo "• Links marked as primary: {$stats->primary_links}\n";
echo "• Products with supplier links: {$stats->products_with_links}\n";
echo "• Products with primary suppliers: {$stats->products_with_primary}\n\n";

echo "3️⃣ IDENTIFYING CONFLICT SCENARIOS:\n";
echo "----------------------------------\n";

// Find products where primary supplier is NOT the cheapest
$db->query("
    WITH ranked_suppliers AS (
        SELECT 
            ps.product_id,
            ps.supplier_id,
            s.supplier_name,
            ps.purchase_price,
            ps.is_primary,
            ROW_NUMBER() OVER (PARTITION BY ps.product_id ORDER BY ps.purchase_price ASC) as price_rank
        FROM product_suppliers ps
        JOIN suppliers s ON ps.supplier_id = s.supplier_id
        WHERE ps.is_active = 1 AND s.status = 'active'
    ),
    primary_suppliers AS (
        SELECT 
            product_id,
            supplier_id as primary_supplier_id,
            supplier_name as primary_supplier_name,
            purchase_price as primary_price
        FROM ranked_suppliers 
        WHERE is_primary = 1
    ),
    cheapest_suppliers AS (
        SELECT 
            product_id,
            supplier_id as cheapest_supplier_id,
            supplier_name as cheapest_supplier_name,
            purchase_price as cheapest_price
        FROM ranked_suppliers 
        WHERE price_rank = 1
    )
    SELECT 
        p.product_id,
        p.product_name,
        ps.primary_supplier_name,
        ps.primary_price,
        cs.cheapest_supplier_name,
        cs.cheapest_price,
        (ps.primary_price - cs.cheapest_price) as price_difference,
        ROUND(((ps.primary_price - cs.cheapest_price) / ps.primary_price) * 100, 2) as savings_percent
    FROM products p
    LEFT JOIN primary_suppliers ps ON p.product_id = ps.product_id
    LEFT JOIN cheapest_suppliers cs ON p.product_id = cs.product_id
    WHERE ps.primary_supplier_id != cs.cheapest_supplier_id
    AND ps.primary_price > cs.cheapest_price
    ORDER BY price_difference DESC
    LIMIT 10
");

$db->execute();
$conflicts = $db->resultSet();

echo "Products where PRIMARY supplier is NOT the CHEAPEST:\n";
if (count($conflicts) > 0) {
    foreach ($conflicts as $conflict) {
        echo "• {$conflict->product_name} (ID: {$conflict->product_id})\n";
        echo "  - Primary: {$conflict->primary_supplier_name} @ ₹{$conflict->primary_price}\n";
        echo "  - Cheapest: {$conflict->cheapest_supplier_name} @ ₹{$conflict->cheapest_price}\n";
        echo "  - Potential savings: ₹{$conflict->price_difference} ({$conflict->savings_percent}%)\n\n";
    }
} else {
    echo "• No conflicts found (primary suppliers are also cheapest)\n\n";
}

echo "4️⃣ CHECKING SYSTEM BEHAVIOR INCONSISTENCIES:\n";
echo "--------------------------------------------\n";

// Check where different parts of system would choose different suppliers
echo "Areas using PRIMARY supplier preference:\n";
echo "• Product model getProductsPaginated() - uses primary_purchase_price\n";
echo "• Purchase order manual creation - may default to primary\n";
echo "• Supplier linking - sets is_primary flag\n";
echo "• Product creation - assigns primary supplier\n\n";

echo "Areas using CHEAPEST supplier preference:\n";
echo "• BotController::findCheapestSupplier() - ignores is_primary\n";
echo "• BotController::getAllSuppliersForProduct() - orders by price\n";
echo "• Automated purchase bot - always buys cheapest\n";
echo "• Product model query comment: 'Choose cheapest supplier (ignore is_primary preference)'\n\n";

echo "5️⃣ DATABASE QUERY EVIDENCE:\n";
echo "---------------------------\n";

// Show actual queries that conflict
echo "CONFLICTING QUERY PATTERNS FOUND:\n\n";

echo "A) Product.php line 48 comment:\n";
echo "   '-- Choose cheapest supplier (ignore is_primary preference)'\n\n";

echo "B) ProductSupplier.php getPrimarySupplier():\n";
echo "   'WHERE ps.is_primary = 1'\n\n";

echo "C) BotController findCheapestSupplier():\n";
echo "   'ORDER BY ps.purchase_price ASC LIMIT 1'\n\n";

echo "D) Purchase add.php JavaScript:\n";
echo "   'if (s.is_primary) opt.selected = true;'\n\n";

echo "6️⃣ RECOMMENDED SOLUTION:\n";
echo "------------------------\n";

echo "OPTION 1: Remove primary supplier concept entirely\n";
echo "✅ Pros: Consistent automated cheapest-price purchasing\n";
echo "❌ Cons: No way to prefer specific suppliers for quality/reliability\n\n";

echo "OPTION 2: Smart supplier selection system\n";
echo "✅ Pros: Context-aware decisions (urgent vs bulk vs normal orders)\n";
echo "✅ Pros: Configurable business rules (price vs quality vs delivery)\n";
echo "✅ Pros: Maintains automation while allowing preferences\n";
echo "❌ Cons: More complex implementation\n\n";

echo "OPTION 3: Hybrid approach\n";
echo "✅ Pros: Keep primary for manual orders, cheapest for automated\n";
echo "❌ Cons: Maintains inconsistency in system behavior\n\n";

echo "7️⃣ IMPACT ASSESSMENT:\n";
echo "---------------------\n";

// Calculate potential savings if system always used cheapest
$db->query("
    SELECT 
        SUM(ps.primary_price - cs.cheapest_price) as total_potential_savings,
        COUNT(*) as affected_products,
        AVG(ps.primary_price - cs.cheapest_price) as avg_savings_per_product
    FROM (
        WITH ranked_suppliers AS (
            SELECT 
                ps.product_id,
                ps.supplier_id,
                ps.purchase_price,
                ps.is_primary,
                ROW_NUMBER() OVER (PARTITION BY ps.product_id ORDER BY ps.purchase_price ASC) as price_rank
            FROM product_suppliers ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            WHERE ps.is_active = 1 AND s.status = 'active'
        ),
        primary_suppliers AS (
            SELECT 
                product_id,
                purchase_price as primary_price
            FROM ranked_suppliers 
            WHERE is_primary = 1
        ),
        cheapest_suppliers AS (
            SELECT 
                product_id,
                purchase_price as cheapest_price
            FROM ranked_suppliers 
            WHERE price_rank = 1
        )
        SELECT 
            ps.primary_price,
            cs.cheapest_price
        FROM primary_suppliers ps
        JOIN cheapest_suppliers cs ON ps.product_id = cs.product_id
        WHERE ps.primary_price > cs.cheapest_price
    ) subquery
");

$db->execute();
$impact = $db->single();

if ($impact && $impact->total_potential_savings > 0) {
    echo "Potential cost savings if always using cheapest supplier:\n";
    echo "• Total potential savings: ₹" . number_format($impact->total_potential_savings, 2) . "\n";
    echo "• Affected products: {$impact->affected_products}\n";
    echo "• Average savings per product: ₹" . number_format($impact->avg_savings_per_product, 2) . "\n\n";
} else {
    echo "• No significant cost impact identified\n\n";
}

echo "8️⃣ CODE LOCATIONS TO MODIFY:\n";
echo "----------------------------\n";

echo "If removing primary supplier concept:\n";
echo "• app/models/Product.php - remove is_primary logic\n";
echo "• app/models/ProductSupplier.php - remove getPrimarySupplier()\n";
echo "• app/controllers/ProductsController.php - remove setPrimarySupplier()\n";
echo "• app/controllers/SuppliersController.php - remove primary logic\n";
echo "• app/views/products/view.php - remove primary badge\n";
echo "• app/views/purchases/add.php - remove primary selection\n";
echo "• database schema - remove is_primary column\n\n";

echo "If implementing smart selection:\n";
echo "• Create SupplierSelector service class\n";
echo "• Add business rules configuration\n";
echo "• Update BotController to use smart selection\n";
echo "• Update purchase order creation\n";
echo "• Add admin panel for rule configuration\n\n";

echo "✅ ANALYSIS COMPLETE\n";
echo "The system has clear conflicts between manual primary supplier preferences\n";
echo "and automated cheapest-price purchasing. Recommend implementing Option 2\n";
echo "(Smart supplier selection) for best long-term solution.\n";
?>