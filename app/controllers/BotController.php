<?php

class BotController extends Controller
{
    private $productModel;
    private $customerModel;
    private $supplierModel;
    private $inventoryModel;
    private $saleModel;
    private $purchaseModel;
    private $db;

    public function __construct()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            // If this is an AJAX request, return a JSON 401 instead of redirecting
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json', true, 401);
                echo json_encode(['success' => false, 'message' => 'Authentication required']);
                exit();
            }

            redirect('users/login');
        }

        // Check permissions
        if (!hasPermission('admin') && !hasPermission('bot')) {
            // If AJAX, return JSON 403
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json', true, 403);
                echo json_encode(['success' => false, 'message' => 'Permission denied']);
                exit();
            }

            redirect('pages/unauthorized');
        }

        $this->productModel = $this->model('Product');
        $this->customerModel = $this->model('Customer');
        $this->supplierModel = $this->model('Supplier');
        $this->inventoryModel = $this->model('Inventory');
        $this->saleModel = $this->model('Sale');
        $this->purchaseModel = $this->model('Purchase');
        $this->db = new Database();
    }

    /**
     * Main bot dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Bot Automation Dashboard',
            'bots' => $this->getBotConfigurations(),
            'system_stats' => $this->getSystemStats()
        ];

        $this->view('admin/bot_dashboard', $data);
    }

    /**
     * Get bot configurations
     */
    private function getBotConfigurations()
    {
        return [
            'purchase_bot' => [
                'id' => 'purchase_bot',
                'name' => 'Purchase Order Bot',
                'description' => 'Automatically creates purchase orders when inventory is low',
                'status' => 'inactive',
                'interval' => 30, // seconds
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-shopping-cart',
                'color' => 'primary'
            ],
            'receiving_bot' => [
                'id' => 'receiving_bot',
                'name' => 'Receiving Bot',
                'description' => 'Simulates receiving inventory and updating stock levels',
                'status' => 'inactive',
                'interval' => 45,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-truck',
                'color' => 'info'
            ],
            'sales_bot' => [
                'id' => 'sales_bot',
                'name' => 'Sales Bot',
                'description' => 'Generates random sales transactions with customers',
                'status' => 'inactive',
                'interval' => 20,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-cash-register',
                'color' => 'success'
            ],
            'customer_bot' => [
                'id' => 'customer_bot',
                'name' => 'Customer Bot',
                'description' => 'Creates new customers and updates customer data',
                'status' => 'inactive',
                'interval' => 60,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-users',
                'color' => 'warning'
            ],
            'inventory_bot' => [
                'id' => 'inventory_bot',
                'name' => 'Inventory Bot',
                'description' => 'Performs inventory adjustments and cycle counts',
                'status' => 'inactive',
                'interval' => 40,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-boxes',
                'color' => 'secondary'
            ],
            'notification_bot' => [
                'id' => 'notification_bot',
                'name' => 'Notification Bot',
                'description' => 'Sends alerts for low stock, overdue orders, etc.',
                'status' => 'inactive',
                'interval' => 25,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-bell',
                'color' => 'danger'
            ],
            'pricing_bot' => [
                'id' => 'pricing_bot',
                'name' => 'Price Bot',
                'description' => 'Automatically optimizes product pricing based on margin and market data',
                'status' => 'inactive',
                'interval' => 60,
                'last_run' => null,
                'actions_count' => 0,
                'icon' => 'fas fa-tags',
                'color' => 'warning'
            ]
        ];
    }

    /**
     * Get system statistics
     */
    private function getSystemStats()
    {
        return [
            'total_products' => $this->productModel->getTotalProducts() ?? 0,
            'total_customers' => $this->customerModel->getTotalCustomers() ?? 0,
            'total_suppliers' => $this->supplierModel->getTotalSuppliers() ?? 0,
            'low_stock_items' => $this->getLowInventoryCount() ?? 0,
            'out_of_stock_items' => $this->getOutOfInventoryCount() ?? 0,
            'pending_orders' => $this->purchaseModel->getPendingOrdersCount() ?? 0,
            'daily_sales' => $this->saleModel->getTodaysSalesCount() ?? 0
        ];
    }

    /**
     * Get low inventory count using same method as Hardware Store Dashboard
     */
    private function getLowInventoryCount()
    {
        try {
            $this->db->query("
                SELECT COUNT(DISTINCT p.product_id) as total 
                FROM products p
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.is_active = 1
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) > 0
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
            ");
            $this->db->execute();
            $result = $this->db->single();
            return $result && $result->total !== null ? $result->total : 0;
        } catch (Exception $e) {
            error_log("Error getting low inventory count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get out of inventory count using same method as Hardware Store Dashboard
     */
    private function getOutOfInventoryCount()
    {
        try {
            $this->db->query("
                SELECT COUNT(DISTINCT p.product_id) as total 
                FROM products p
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.is_active = 1
                AND p.deleted_at IS NULL
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
            ");
            $this->db->execute();
            $result = $this->db->single();
            return $result && $result->total !== null ? $result->total : 0;
        } catch (Exception $e) {
            error_log("Error getting out of inventory count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get products that are completely out of stock (0 quantity)
     * regardless of their reorder level - these should be highest priority
     */
    private function getOutOfStockProducts()
    {
        try {
            $this->db->query("
                SELECT 
                    p.product_id, 
                    p.product_name, 
                    COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as stock_quantity,
                    p.reorder_level
                FROM products p
                WHERE p.is_active = 1 
                AND p.deleted_at IS NULL
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= 0
                ORDER BY stock_quantity ASC
                LIMIT 50
            ");
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting out of stock products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Start a specific bot
     */
    public function startBot()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $botId = $_POST['bot_id'] ?? '';

        if (empty($botId)) {
            echo json_encode(['success' => false, 'message' => 'Bot ID is required']);
            return;
        }

        // Store bot status in session for now (in production, use database)
        if (!isset($_SESSION['bot_status'])) {
            $_SESSION['bot_status'] = [];
        }

        $_SESSION['bot_status'][$botId] = [
            'status' => 'active',
            'started_at' => time(),
            'actions_count' => 0
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Bot started successfully',
            'bot_id' => $botId,
            'status' => 'active'
        ]);
    }

    /**
     * Stop a specific bot
     */
    public function stopBot()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $botId = $_POST['bot_id'] ?? '';

        if (empty($botId)) {
            echo json_encode(['success' => false, 'message' => 'Bot ID is required']);
            return;
        }

        if (!isset($_SESSION['bot_status'])) {
            $_SESSION['bot_status'] = [];
        }

        $_SESSION['bot_status'][$botId] = [
            'status' => 'inactive',
            'stopped_at' => time()
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Bot stopped successfully',
            'bot_id' => $botId,
            'status' => 'inactive'
        ]);
    }

    /**
     * Execute bot action
     */
    public function executeAction()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $botId = $_POST['bot_id'] ?? '';

        if (empty($botId)) {
            echo json_encode(['success' => false, 'message' => 'Bot ID is required']);
            return;
        }

        $result = $this->performBotAction($botId);

        echo json_encode($result);
    }

    /**
     * Perform specific bot action based on bot type
     */
    private function performBotAction($botId)
    {
        switch ($botId) {
            case 'purchase_bot':
                return $this->executePurchaseBot();

            case 'receiving_bot':
                return $this->executeReceivingBot();

            case 'sales_bot':
                return $this->executeSalesBot();

            case 'customer_bot':
                return $this->executeCustomerBot();

            case 'inventory_bot':
                return $this->executeInventoryBot();

            case 'notification_bot':
                return $this->executeNotificationBot();

            case 'pricing_bot':
                return $this->executePricingBot();

            default:
                return ['success' => false, 'message' => 'Unknown bot type'];
        }
    }

    /**
     * Purchase Bot Logic - Smart purchasing from cheapest suppliers
     * Prioritizes 0-stock products regardless of reorder level
     */
    private function executePurchaseBot()
    {
        try {
            // First priority: Get all products with 0 stock (regardless of reorder level)
            $outOfStockProducts = $this->getOutOfStockProducts();

            // Second priority: Get low stock products based on reorder level
            $lowStockProducts = $this->inventoryModel->getLowStockProducts(10);

            // Combine them with out-of-stock taking priority
            $allTargetProducts = array_merge($outOfStockProducts, $lowStockProducts);

            // Remove duplicates (in case a product appears in both lists)
            $uniqueProducts = [];
            $seenIds = [];
            foreach ($allTargetProducts as $product) {
                if (!in_array($product->product_id, $seenIds)) {
                    $uniqueProducts[] = $product;
                    $seenIds[] = $product->product_id;
                }
            }

            if (empty($uniqueProducts)) {
                return [
                    'success' => true,
                    'message' => 'No out-of-stock or low stock items found',
                    'action' => 'checked_inventory',
                    'details' => 'All products have sufficient stock'
                ];
            }

            // Smart product selection to avoid ordering the same item repeatedly
            $product = $this->selectNextProductForPurchase($uniqueProducts);

            if (!$product) {
                return [
                    'success' => true,
                    'message' => 'All target products recently ordered',
                    'action' => 'skipped_ordering',
                    'details' => 'Waiting for previous orders to be received before reordering'
                ];
            }

            // Find optimal supplier for this product using smart selection
            $allSuppliers = $this->getAllSuppliersForProduct($product->product_id);

            // Determine urgency based on stock level
            $currentStock = $product->current_stock ?? 0;
            $urgency = ($currentStock <= 5) ? 'urgent' : 'normal';

            // Calculate optimal order quantity first
            $optimalStock = max(30, ($product->reorder_level ?? 20) * 2);
            $minOrderQty = 10; // Default minimum
            $orderQuantity = max($minOrderQty, $optimalStock - $currentStock);
            $orderQuantity = min($orderQuantity, 100); // Cap at 100 units
            $orderQuantity = max($orderQuantity, 5);   // Minimum 5 units

            $optimalSupplier = $this->findOptimalSupplier($product->product_id, $orderQuantity, $urgency);

            if (!$optimalSupplier || ($optimalSupplier->purchase_price ?? 0) <= 0) {
                // Fallback to default supplier with validation
                $fallbackPrice = max($product->purchase_price ?? 10, $product->selling_price * 0.6 ?? 10);
                $optimalSupplier = (object) [
                    'supplier_id' => 1,
                    'purchase_price' => $fallbackPrice,
                    'supplier_name' => 'Default Supplier (Fallback)',
                    'selection_reasoning' => 'Fallback supplier - no optimal supplier found'
                ];
                $allSuppliers = [$optimalSupplier]; // Create array for savings calculation
            }

            // Calculate smart order quantity based on stock level and product characteristics
            $currentStock = $product->stock_quantity ?? 0;
            $minOrderQty = $optimalSupplier->min_order_quantity ?? 20;
            $reorderLevel = $product->reorder_level ?? 15; // Use product's reorder level if available

            // Calculate monthly demand based on product price (higher priced = lower volume)
            $productPrice = $product->selling_price ?? 50;
            if ($productPrice <= 20) {
                $monthlyDemand = rand(15, 25); // High volume for cheap items
            } elseif ($productPrice <= 50) {
                $monthlyDemand = rand(8, 15); // Medium volume for mid-price items
            } else {
                $monthlyDemand = rand(3, 8); // Low volume for expensive items
            }

            // Order enough to reach optimal stock level (2-4 months supply)
            $monthsSupply = rand(2, 4);
            $optimalStock = $monthlyDemand * $monthsSupply;
            $orderQuantity = max($minOrderQty, $optimalStock - $currentStock);

            // Ensure reasonable quantity bounds
            $orderQuantity = min($orderQuantity, 100); // Cap at 100 units
            $orderQuantity = max($orderQuantity, 5);   // Minimum 5 units

            $result = $this->createSmartPurchaseOrder($product, $orderQuantity, $optimalSupplier);

            // Update session counter
            if (!isset($_SESSION['bot_status']['purchase_bot']['actions_count'])) {
                $_SESSION['bot_status']['purchase_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['purchase_bot']['actions_count']++;

            $savingsAmount = $this->calculateSavings($optimalSupplier, $allSuppliers, $orderQuantity);
            $savingsPercent = $savingsAmount > 0 ? round(($savingsAmount / ($orderQuantity * $optimalSupplier->purchase_price)) * 100, 2) : 0;

            // Include supplier selection reasoning in the details
            $selectionReason = isset($optimalSupplier->selection_reasoning) ? " (Selected: {$optimalSupplier->selection_reasoning})" : "";

            return [
                'success' => true,
                'message' => 'Smart purchase order created',
                'action' => 'created_purchase_order',
                'details' => "Ordered {$orderQuantity} units of {$product->product_name} from {$optimalSupplier->supplier_name} (₹{$optimalSupplier->purchase_price}/unit, ₹{$savingsAmount} savings){$selectionReason}",
                'product_name' => $product->product_name,
                'quantity' => $orderQuantity,
                'supplier_name' => $optimalSupplier->supplier_name,
                'unit_cost' => $optimalSupplier->purchase_price,
                'total_cost' => $orderQuantity * $optimalSupplier->purchase_price,
                'current_stock' => $currentStock,
                'savings_amount' => $savingsAmount,
                'savings_percent' => $savingsPercent,
                'selection_reasoning' => $optimalSupplier->selection_reasoning ?? 'Standard selection'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Purchase bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Smart product selection for purchase bot to avoid ordering same item repeatedly
     * 
     * @param array $lowStockProducts List of products with low stock
     * @return object|null Selected product or null if all recently ordered
     */
    private function selectNextProductForPurchase($lowStockProducts)
    {
        // Check recent purchase orders to avoid ordering same products repeatedly
        $recentOrders = $this->getRecentBotPurchaseOrders();
        $recentlyOrderedProductIds = array_column($recentOrders, 'product_id');

        // Filter out products that were ordered in the last hour
        $availableProducts = array_filter($lowStockProducts, function ($product) use ($recentlyOrderedProductIds) {
            return !in_array($product->product_id, $recentlyOrderedProductIds);
        });

        // If no products available (all recently ordered), allow reordering with longer gap
        if (empty($availableProducts)) {
            // Check for products ordered more than 30 minutes ago
            $olderRecentOrders = $this->getRecentBotPurchaseOrders(30); // 30 minutes
            $olderOrderedProductIds = array_column($olderRecentOrders, 'product_id');

            $availableProducts = array_filter($lowStockProducts, function ($product) use ($olderOrderedProductIds) {
                return !in_array($product->product_id, $olderOrderedProductIds);
            });
        }

        // If still no products available, just pick one (failsafe)
        if (empty($availableProducts)) {
            $availableProducts = $lowStockProducts;
        }

        // Smart rotation: For products with same stock level (like all 0-stock items),
        // use round-robin selection to ensure fair distribution
        if (!isset($_SESSION['bot_product_rotation_index'])) {
            $_SESSION['bot_product_rotation_index'] = 0;
        }

        // Group products by stock level
        $stockGroups = [];
        foreach ($availableProducts as $product) {
            $stockLevel = $product->stock_quantity ?? 0;
            if (!isset($stockGroups[$stockLevel])) {
                $stockGroups[$stockLevel] = [];
            }
            $stockGroups[$stockLevel][] = $product;
        }

        // Sort stock groups by level (lowest first)
        ksort($stockGroups);

        $selectedProduct = null;

        // For the lowest stock group, use round-robin selection
        foreach ($stockGroups as $stockLevel => $products) {
            if (!empty($products)) {
                // Sort products in this group by ID for consistent ordering
                usort($products, function ($a, $b) {
                    return $a->product_id <=> $b->product_id;
                });

                // Use round-robin selection within this stock level
                $rotationIndex = $_SESSION['bot_product_rotation_index'] % count($products);
                $selectedProduct = $products[$rotationIndex];
                break; // Take from lowest stock level only
            }
        }

        // Increment rotation index for next run
        $_SESSION['bot_product_rotation_index'] = ($_SESSION['bot_product_rotation_index'] ?? 0) + 1;

        // Return the selected product
        return $selectedProduct;
    }

    /**
     * Get recent bot purchase orders to avoid duplicate ordering
     * 
     * @param int $minutesBack How many minutes back to check (default 60)
     * @return array Recent purchase order items
     */
    private function getRecentBotPurchaseOrders($minutesBack = 60)
    {
        try {
            $this->db->query("
                SELECT DISTINCT pi.product_id, p.product_name, pur.created_at
                FROM purchase_items pi
                INNER JOIN purchases pur ON pi.purchase_id = pur.purchase_id
                LEFT JOIN products p ON pi.product_id = p.product_id
                WHERE pur.po_number LIKE 'BOT-PO-%'
                AND pur.created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
                ORDER BY pur.created_at DESC
            ");
            $this->db->bind(':minutes', $minutesBack);
            $this->db->execute();

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log('Error getting recent bot purchases: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sales Bot Logic - Smart selling prioritizing higher profit margins
     */
    private function executeSalesBot()
    {
        try {
            // Get available products with profit margin analysis
            $products = $this->productModel->getProductsWithProfitMargins(10);

            if (empty($products)) {
                return [
                    'success' => true,
                    'message' => 'No products available for sale',
                    'action' => 'skipped_sale',
                    'details' => 'Need products with valid pricing'
                ];
            }

            // Create walk-in customer for bot sales
            $walkInCustomer = $this->createWalkInCustomer();

            // Filter out products with negative profit (don't sell at a loss)
            $profitableProducts = array_filter($products, function ($product) {
                $profitMargin = $this->calculateProfitMargin($product);
                return $profitMargin > 0; // Only sell products with positive profit
            });

            if (empty($profitableProducts)) {
                return [
                    'success' => true,
                    'message' => 'No profitable products available',
                    'action' => 'skipped_sale',
                    'details' => 'All products would result in losses'
                ];
            }

            // Realistic sales logic: cheaper items sell more frequently
            $selectedProduct = $this->selectProductByRealWorldFrequency($profitableProducts);

            // Smart quantity based on price category and inventory
            $availableStock = $selectedProduct->current_stock ?? 5;
            $productPrice = $selectedProduct->selling_price ?? 50;

            // Realistic quantity patterns based on price
            if ($productPrice <= 50) {
                // Cheap items: customers buy more units
                $maxSaleQty = min($availableStock, rand(3, 8));
            } elseif ($productPrice <= 200) {
                // Mid-range items: moderate quantities
                $maxSaleQty = min($availableStock, rand(1, 4));
            } else {
                // Expensive items: usually single purchases
                $maxSaleQty = min($availableStock, rand(1, 2));
            }

            $quantity = max(1, $maxSaleQty);

            $saleResult = $this->createSmartSale($walkInCustomer, $selectedProduct, $quantity);

            // Update session counter
            if (!isset($_SESSION['bot_status']['sales_bot']['actions_count'])) {
                $_SESSION['bot_status']['sales_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['sales_bot']['actions_count']++;

            $profitMargin = $this->calculateProfitMargin($selectedProduct);
            $unitCostForReport = isset($selectedProduct->current_average_cost) && floatval($selectedProduct->current_average_cost) > 0 ? floatval($selectedProduct->current_average_cost) : floatval($selectedProduct->purchase_price ?? 0);
            $totalRevenue = $quantity * ($selectedProduct->selling_price ?? 0);
            $totalCost = $quantity * $unitCostForReport;
            $totalProfit = $totalRevenue - $totalCost;

            return [
                'success' => true,
                'message' => 'Strategic walk-in sale completed',
                'action' => 'created_sale',
                'details' => "Sold {$quantity} units of {$selectedProduct->product_name} to {$walkInCustomer->name} (Margin: {$profitMargin}%, Profit: \${$totalProfit})",
                'customer_name' => $walkInCustomer->name,
                'product_name' => $selectedProduct->product_name,
                'quantity' => $quantity,
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => $profitMargin,
                'unit_cost' => $unitCostForReport ?? 0,
                'unit_price' => $selectedProduct->selling_price ?? 0
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Sales bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Receiving Bot Logic - Processes actual purchase orders
     */
    private function executeReceivingBot()
    {
        try {
            // Get pending purchase items that need receiving
            $pendingItems = $this->getPendingPurchaseItems();

            if (empty($pendingItems)) {
                return [
                    'success' => true,
                    'message' => 'No purchase items pending receiving',
                    'action' => 'skipped_receiving'
                ];
            }

            // Select a random item to receive (simulate receiving workflow)
            $item = $pendingItems[array_rand($pendingItems)];

            // Calculate how much to receive (FULL quantity for maximum efficiency)
            $remainingQty = $item->quantity - $item->received_quantity;
            $receiveQty = $remainingQty; // Receive all remaining quantity

            // Process the receiving
            $result = $this->processReceiving($item, $receiveQty);

            if ($result['success']) {
                // Update session counter
                if (!isset($_SESSION['bot_status']['receiving_bot']['actions_count'])) {
                    $_SESSION['bot_status']['receiving_bot']['actions_count'] = 0;
                }
                $_SESSION['bot_status']['receiving_bot']['actions_count']++;

                return [
                    'success' => true,
                    'message' => 'Purchase item received successfully',
                    'action' => 'received_purchase_item',
                    'details' => "Received {$receiveQty} units of {$item->product_name} from PO #{$item->po_number}",
                    'product_name' => $item->product_name,
                    'quantity' => $receiveQty,
                    'po_number' => $item->po_number,
                    'remaining_qty' => $remainingQty - $receiveQty
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to process receiving: ' . $result['message'],
                    'action' => 'error'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Receiving bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Customer Bot Logic
     */
    private function executeCustomerBot()
    {
        try {
            $customerNames = ['John Smith', 'Sarah Johnson', 'Mike Wilson', 'Emily Davis', 'Chris Brown', 'Jessica Miller', 'David Garcia', 'Lisa Anderson'];
            $customerName = $customerNames[array_rand($customerNames)];

            // Create a new customer
            $customerId = $this->createMockCustomer($customerName);

            // Update session counter
            if (!isset($_SESSION['bot_status']['customer_bot']['actions_count'])) {
                $_SESSION['bot_status']['customer_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['customer_bot']['actions_count']++;

            return [
                'success' => true,
                'message' => 'New customer added',
                'action' => 'created_customer',
                'details' => "Added customer: {$customerName}",
                'customer_name' => $customerName,
                'customer_id' => $customerId
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Customer bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Inventory Bot Logic
     */
    private function executeInventoryBot()
    {
        try {
            $actions = ['cycle_count', 'adjustment', 'audit'];
            $action = $actions[array_rand($actions)];

            $products = $this->productModel->getRandomProducts(3);

            if (empty($products)) {
                return [
                    'success' => true,
                    'message' => 'No products for inventory action',
                    'action' => 'skipped_inventory'
                ];
            }

            $product = $products[array_rand($products)];

            switch ($action) {
                case 'cycle_count':
                    $result = $this->performCycleCount($product);
                    break;
                case 'adjustment':
                    $result = $this->performInventoryAdjustment($product);
                    break;
                case 'audit':
                    $result = $this->performInventoryAudit($product);
                    break;
            }

            // Update session counter
            if (!isset($_SESSION['bot_status']['inventory_bot']['actions_count'])) {
                $_SESSION['bot_status']['inventory_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['inventory_bot']['actions_count']++;

            return $result;

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Inventory bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Notification Bot Logic
     */
    private function executeNotificationBot()
    {
        try {
            $notifications = [
                'Low stock alert for critical items',
                'Daily sales report ready',
                'Pending purchase orders require approval',
                'Customer payment overdue reminder',
                'Weekly inventory audit completed',
                'New supplier quote received'
            ];

            $notification = $notifications[array_rand($notifications)];

            // Log the notification (in production, send to notification system)
            error_log("Bot Notification: " . $notification);

            // Update session counter
            if (!isset($_SESSION['bot_status']['notification_bot']['actions_count'])) {
                $_SESSION['bot_status']['notification_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['notification_bot']['actions_count']++;

            return [
                'success' => true,
                'message' => 'Notification sent',
                'action' => 'sent_notification',
                'details' => $notification,
                'notification_text' => $notification
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Notification bot error: ' . $e->getMessage(),
                'action' => 'error'
            ];
        }
    }

    /**
     * Get bot status
     */
    public function getBotStatus()
    {
        header('Content-Type: application/json');

        $botId = $_GET['bot_id'] ?? '';

        if (empty($botId)) {
            echo json_encode(['success' => false, 'message' => 'Bot ID is required']);
            return;
        }

        $status = $_SESSION['bot_status'][$botId] ?? ['status' => 'inactive', 'actions_count' => 0];

        echo json_encode([
            'success' => true,
            'bot_id' => $botId,
            'status' => $status['status'] ?? 'inactive',
            'actions_count' => $status['actions_count'] ?? 0,
            'last_action' => $status['last_action'] ?? null
        ]);
    }

    // Helper methods for intelligent business logic

    /**
     * Find optimal supplier for a product using smart selection
     * Replaces the old findCheapestSupplier with context-aware selection
     */
    private function findOptimalSupplier($productId, $quantity = 1, $urgency = 'normal')
    {
        try {
            // Use smart supplier selection instead of just cheapest
            require_once APPROOT . '/services/SupplierSelector.php';
            $selector = new SupplierSelector($this->db);

            $optimal = $selector->getOptimalSupplier($productId, $quantity, $urgency);

            if ($optimal) {
                // Convert to format expected by existing code
                return (object) [
                    'supplier_id' => $optimal->supplier_id,
                    'supplier_name' => $optimal->supplier_name,
                    'purchase_price' => $optimal->purchase_price,
                    'min_order_quantity' => $optimal->min_order_quantity,
                    'selection_score' => $optimal->selection_score,
                    'selection_reasoning' => $optimal->selection_reasoning
                ];
            }
        } catch (Exception $e) {
            error_log('Error in smart supplier selection: ' . $e->getMessage());
        }

        // Fallback to old cheapest supplier logic if smart selection fails
        return $this->findCheapestSupplierFallback($productId);
    }

    /**
     * Legacy cheapest supplier finder (fallback only)
     */
    private function findCheapestSupplierFallback($productId)
    {
        try {
            // Try to get real supplier data from database
            $this->db->query("
                SELECT 
                    s.supplier_id,
                    s.supplier_name,
                    ps.purchase_price,
                    ps.min_order_quantity
                FROM suppliers s
                INNER JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
                WHERE ps.product_id = :product_id
                AND s.status = 'active'
                ORDER BY ps.purchase_price ASC
                LIMIT 1
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $supplier = $this->db->single();

            if ($supplier) {
                return $supplier;
            }
        } catch (Exception $e) {
            error_log('Error getting supplier data: ' . $e->getMessage());
        }

        // Fallback: Get product's base price and create dynamic supplier data
        try {
            $this->db->query("SELECT purchase_price, selling_price FROM products WHERE product_id = :product_id");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $productData = $this->db->single();

            if ($productData && $productData->purchase_price > 0) {
                // Create realistic supplier pricing based on product's purchase price
                $basePrice = $productData->purchase_price;
                $suppliers = [
                    (object) [
                        'supplier_id' => 1,
                        'supplier_name' => 'Budget Supplies Co.',
                        'purchase_price' => round($basePrice * 0.85, 2), // 15% discount
                        'min_order_quantity' => 20
                    ],
                    (object) [
                        'supplier_id' => 2,
                        'supplier_name' => 'Quality Parts Ltd.',
                        'purchase_price' => round($basePrice * 0.95, 2), // 5% discount
                        'min_order_quantity' => 10
                    ],
                    (object) [
                        'supplier_id' => 3,
                        'supplier_name' => 'Premium Hardware Inc.',
                        'purchase_price' => round($basePrice * 1.05, 2), // 5% premium
                        'min_order_quantity' => 5
                    ]
                ];

                // Return the cheapest (Budget Supplies Co.)
                return $suppliers[0];
            }
        } catch (Exception $e) {
            error_log('Error getting product pricing: ' . $e->getMessage());
        }

        // Final fallback with random pricing
        $randomPrice = round(rand(500, 2000) / 100, 2); // $5.00 to $20.00
        return (object) [
            'supplier_id' => 1,
            'supplier_name' => 'Default Supplier',
            'purchase_price' => $randomPrice,
            'min_order_quantity' => rand(10, 30)
        ];
    }

    /**
     * Get all suppliers for a product
     */
    private function getAllSuppliersForProduct($productId)
    {
        try {
            // Try to get real supplier data from database
            $this->db->query("
                SELECT 
                    s.supplier_id,
                    s.supplier_name,
                    ps.purchase_price,
                    ps.min_order_quantity
                FROM suppliers s
                INNER JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
                WHERE ps.product_id = :product_id
                AND s.status = 'active'
                ORDER BY ps.purchase_price ASC
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $suppliers = $this->db->resultSet();

            if (!empty($suppliers)) {
                return $suppliers;
            }
        } catch (Exception $e) {
            error_log('Error getting all suppliers: ' . $e->getMessage());
        }

        // Fallback: Get product's base price and create dynamic supplier options
        try {
            $this->db->query("SELECT purchase_price FROM products WHERE product_id = :product_id");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $productData = $this->db->single();

            if ($productData && $productData->purchase_price > 0) {
                $basePrice = $productData->purchase_price;

                return [
                    (object) [
                        'supplier_id' => 1,
                        'supplier_name' => 'Budget Supplies Co.',
                        'purchase_price' => round($basePrice * 0.85, 2),
                        'min_order_quantity' => 20
                    ],
                    (object) [
                        'supplier_id' => 2,
                        'supplier_name' => 'Quality Parts Ltd.',
                        'purchase_price' => round($basePrice * 0.95, 2),
                        'min_order_quantity' => 10
                    ],
                    (object) [
                        'supplier_id' => 3,
                        'supplier_name' => 'Premium Hardware Inc.',
                        'purchase_price' => round($basePrice * 1.05, 2),
                        'min_order_quantity' => 5
                    ]
                ];
            }
        } catch (Exception $e) {
            error_log('Error getting product data for suppliers: ' . $e->getMessage());
        }

        // Final fallback
        $randomBase = round(rand(500, 2000) / 100, 2);
        return [
            (object) [
                'supplier_id' => 1,
                'supplier_name' => 'Default Supplier A',
                'purchase_price' => $randomBase,
                'min_order_quantity' => rand(10, 30)
            ]
        ];
    }

    /**
     * Create smart purchase order with supplier optimization
     */
    private function createSmartPurchaseOrder($product, $quantity, $supplier)
    {
        try {
            // Create actual purchase order in database
            $purchaseData = [
                'po_number' => 'BOT-PO-' . date('YmdHis') . '-' . rand(1000, 9999),
                'supplier_id' => $supplier->supplier_id,
                'total_amount' => $quantity * $supplier->purchase_price,
                'status' => 'pending',
                'order_date' => date('Y-m-d H:i:s'),
                'expected_date' => date('Y-m-d', strtotime('+7 days')),
                'created_by' => $_SESSION['user_id'] ?? 1,
                'notes' => "Smart purchase order - cheapest supplier selected"
            ];

            $purchaseId = $this->purchaseModel->createPurchase($purchaseData);

            if ($purchaseId) {
                // Create purchase order items
                $itemData = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $supplier->purchase_price
                ];

                $this->purchaseModel->addPurchaseItem($itemData);

                return [
                    'purchase_id' => $purchaseId,
                    'success' => true,
                    'total_cost' => $quantity * $supplier->purchase_price
                ];
            }

            return ['success' => false, 'error' => 'Failed to create purchase order'];

        } catch (Exception $e) {
            error_log('Bot createSmartPurchaseOrder error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate profit margin percentage for a product
     */
    private function calculateProfitMargin($product)
    {
        // Use the best available cost field (current average cost, then primary_purchase_price,
        // then unit_price, then purchase_price). Calculate margin relative to selling price.
        // Treat 0 as missing for current_average_cost
        $cac = isset($product->current_average_cost) ? floatval($product->current_average_cost) : 0;
        $cost = $cac > 0 ? $cac : floatval($product->purchase_price ?? 0);
        $sellingPrice = floatval($product->selling_price ?? 0);

        if ($cost <= 0 || $sellingPrice <= 0) {
            return 0; // Avoid invalid margins
        }

        $profit = $sellingPrice - $cost;
        $marginPercentage = ($profit / $sellingPrice) * 100; // margin as % of selling price

        return round($marginPercentage, 2);
    }

    /**
     * Create intelligent sale with business logic
     */
    private function createSmartSale($customer, $product, $quantity)
    {
        // Calculate sale details
        $unitPrice = floatval($product->selling_price ?? 0);

        // If no selling price, use a reasonable markup over cost (50% markup)
        if ($unitPrice <= 0) {
            $costPrice = floatval($product->purchase_price ?? 0);
            if ($costPrice > 0) {
                $unitPrice = $costPrice * 1.5; // 50% markup
                error_log("No selling price found, using cost + 50% markup: " . $unitPrice);
            } else {
                error_log("ERROR: No selling price or cost found for product: " . $product->product_name);
                return false; // Don't create sale without proper pricing
            }
        }

        $totalAmount = $quantity * $unitPrice;
        $saleDate = date('Y-m-d H:i:s');

        // Create sale record with correct fields
        $saleData = [
            'customer_id' => $customer->customer_id,
            'total_amount' => $totalAmount,
            'payment_mode' => 'cash',
            'sale_date' => $saleDate
        ];

        $saleId = $this->saleModel->addSale($saleData);

        if ($saleId) {
            // Add sale item (discount defaults to 0)
            $itemData = [
                'sale_id' => $saleId,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => 0,
                'total_price' => $totalAmount
            ];

            $this->saleModel->addSaleItem($itemData);

            // Update inventory stock
            // Log inventory state before and after update for debugging
            try {
                $logFile = APPROOT . DS . 'bot_inventory.log';
                $beforeTotal = $this->inventoryModel->getProductTotal($product->product_id);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - createSmartSale before update product_id={$product->product_id} total_inventory=" . ($beforeTotal ?? 'NULL') . "\n", FILE_APPEND);
            } catch (Exception $e) {
                // ignore
            }

            $this->inventoryModel->updateStock($product->product_id, -$quantity);

            try {
                $afterTotal = $this->inventoryModel->getProductTotal($product->product_id);
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " - createSmartSale after update product_id={$product->product_id} total_inventory=" . ($afterTotal ?? 'NULL') . "\n", FILE_APPEND);
            } catch (Exception $e) {
                // ignore
            }

            return [
                'sale_id' => $saleId,
                'success' => true,
                'total_amount' => $totalAmount,
                'profit_margin' => $this->calculateProfitMargin($product)
            ];
        }

        return ['success' => false, 'error' => 'Failed to create sale'];
    }

    /**
     * Calculate potential savings from purchasing decisions
     */
    private function calculateSavings($cheapestSupplier, $allSuppliers, $quantity)
    {
        if (empty($allSuppliers) || count($allSuppliers) < 2) {
            return 0;
        }

        // Find the most expensive supplier
        $maxPrice = 0;
        foreach ($allSuppliers as $supplier) {
            $price = floatval($supplier->purchase_price ?? 0);
            if ($price > $maxPrice) {
                $maxPrice = $price;
            }
        }

        $cheapestPrice = floatval($cheapestSupplier->purchase_price ?? 0);
        $savings = ($maxPrice - $cheapestPrice) * $quantity;

        return round($savings, 2);
    }

    // Helper methods for creating mock data

    private function createMockPurchaseOrder($product, $quantity)
    {
        try {
            // Create actual purchase order in database
            $purchaseData = [
                'supplier_id' => 1, // Default supplier or get from product
                'total_amount' => $quantity * ($product->purchase_price ?? 10),
                'status' => 'pending',
                'order_date' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['user_id'] ?? 1,
                'notes' => 'Auto-generated by Purchase Bot',
                'po_number' => 'BOT-PO-' . date('YmdHis') . '-' . rand(1000, 9999)
            ];

            $purchaseId = $this->purchaseModel->createPurchase($purchaseData);

            if ($purchaseId) {
                // Add purchase item
                $itemData = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $product->purchase_price ?? 10,
                    'total_price' => $quantity * ($product->purchase_price ?? 10)
                ];

                $this->purchaseModel->addPurchaseItem($itemData);
                return $purchaseId;
            }

            return false;
        } catch (Exception $e) {
            error_log('Bot createMockPurchaseOrder error: ' . $e->getMessage());
            return false;
        }
    }

    private function createMockSale($customer, $product, $quantity)
    {
        try {
            // Create actual sale in database
            $saleData = [
                'customer_id' => $customer->customer_id,
                'total_amount' => $quantity * ($product->selling_price ?? 15),
                'payment_mode' => 'cash',
                'sale_date' => date('Y-m-d H:i:s')
            ];

            $saleId = $this->saleModel->addSale($saleData);

            if ($saleId) {
                // Add sale item
                $itemData = [
                    'sale_id' => $saleId,
                    'product_id' => $product->product_id,
                    'quantity' => $quantity,
                    'unit_price' => $product->selling_price ?? 15,
                    'total_price' => $quantity * ($product->selling_price ?? 15)
                ];

                $this->saleModel->addSaleItem($itemData);

                // Update inventory
                $this->inventoryModel->updateStock($product->product_id, -$quantity);

                return $saleId;
            }

            return false;
        } catch (Exception $e) {
            error_log('Bot createMockSale error: ' . $e->getMessage());
            return false;
        }
    }

    private function createMockCustomer($name)
    {
        // In a real implementation, this would create an actual customer
        return rand(1000, 9999);
    }

    private function updateInventoryStock($productId, $quantity)
    {
        try {
            // Update actual inventory stock
            return $this->inventoryModel->updateStock($productId, $quantity);
        } catch (Exception $e) {
            error_log('Bot updateInventoryStock error: ' . $e->getMessage());
            return false;
        }
    }

    private function performCycleCount($product)
    {
        return [
            'success' => true,
            'message' => 'Cycle count completed',
            'action' => 'cycle_count',
            'details' => "Performed cycle count for {$product->product_name}",
            'product_name' => $product->product_name
        ];
    }

    private function performInventoryAdjustment($product)
    {
        $adjustment = rand(-5, 10);
        return [
            'success' => true,
            'message' => 'Inventory adjusted',
            'action' => 'inventory_adjustment',
            'details' => "Adjusted {$product->product_name} by {$adjustment} units",
            'product_name' => $product->product_name,
            'adjustment' => $adjustment
        ];
    }

    private function performInventoryAudit($product)
    {
        return [
            'success' => true,
            'message' => 'Inventory audit completed',
            'action' => 'inventory_audit',
            'details' => "Audited {$product->product_name} - no discrepancies found",
            'product_name' => $product->product_name
        ];
    }

    /**
     * Create walk-in customer for bot sales
     */
    private function createWalkInCustomer()
    {
        // Create a walk-in customer object for bot sales
        return (object) [
            'customer_id' => 1, // Default walk-in customer ID
            'name' => 'Walk-in Customer',
            'customer_name' => 'Walk-in Customer',
            'contact_info' => 'N/A'
        ];
    }

    /**
     * Pricing Bot Logic - Intelligent price optimization with reasoning
     */
    private function executePricingBot()
    {
        try {
            // Get products that need price optimization
            $products = $this->productModel->getProductsForPriceManagement();

            if (empty($products)) {
                return [
                    'success' => true,
                    'message' => 'No products need pricing optimization',
                    'action' => 'skipped_pricing',
                    'details' => 'All products have optimal pricing',
                    'reason' => 'No eligible products found for pricing optimization'
                ];
            }

            // Intelligent product selection with scoring
            $selectedProduct = $this->selectProductForPricing($products);
            $selectionReason = $selectedProduct['reason'];
            $product = $selectedProduct['product'];

            // Calculate optimal price using intelligent algorithm
            $currentPrice = (float) $product->selling_price;
            $costPrice = (float) $product->purchase_price;

            if ($costPrice <= 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot calculate price: invalid cost price',
                    'action' => 'pricing_error',
                    'details' => "Product {$product->product_name} has invalid cost price",
                    'reason' => 'Invalid cost price data'
                ];
            }

            // Smart pricing algorithm with multiple strategies
            $pricingResult = $this->calculateSmartPrice($product, $currentPrice, $costPrice);
            $newPrice = $pricingResult['price'];
            $pricingReason = $pricingResult['reason'];
            $strategy = $pricingResult['strategy'];

            // Only update if price changed significantly (more than 1%)
            $priceChangePercent = abs(($newPrice - $currentPrice) / $currentPrice) * 100;

            if ($priceChangePercent < 1.0) {
                return [
                    'success' => true,
                    'message' => 'Price already optimal',
                    'action' => 'no_price_change',
                    'details' => "Price for {$product->product_name} is within 1% of optimal",
                    'reason' => 'Current price is already optimal (< 1% change needed)',
                    'selection_reason' => $selectionReason,
                    'pricing_strategy' => $strategy
                ];
            }

            // Update the price in database
            $updateResult = $this->productModel->updateProductPrice(
                $product->product_id,
                $newPrice
            );

            if (!$updateResult) {
                return [
                    'success' => false,
                    'message' => 'Failed to update product price',
                    'action' => 'pricing_error',
                    'reason' => 'Database update failed'
                ];
            }

            // Update session counter
            if (!isset($_SESSION['bot_status']['pricing_bot']['actions_count'])) {
                $_SESSION['bot_status']['pricing_bot']['actions_count'] = 0;
            }
            $_SESSION['bot_status']['pricing_bot']['actions_count']++;

            // Calculate profit margin
            $newMargin = (($newPrice - $costPrice) / $newPrice) * 100;
            $priceChange = $newPrice - $currentPrice;
            $changeDirection = $priceChange > 0 ? 'increased' : 'decreased';

            return [
                'success' => true,
                'message' => 'Price optimized successfully',
                'action' => 'price_updated',
                'details' => "Price for {$product->product_name} {$changeDirection} from ₹" . number_format($currentPrice, 2) . " to ₹" . number_format($newPrice, 2) . " (Margin: " . round($newMargin, 1) . "%)",
                'product_name' => $product->product_name,
                'old_price' => $currentPrice,
                'new_price' => $newPrice,
                'price_change' => $priceChange,
                'margin_percent' => round($newMargin, 1),
                'change_percent' => round($priceChangePercent, 2),
                'reason' => $pricingReason,
                'selection_reason' => $selectionReason,
                'pricing_strategy' => $strategy,
                // Add pricing logic data for frontend panel
                'pricing_logic' => [
                    'decision' => [
                        'productName' => $product->product_name,
                        'strategy' => $strategy,
                        'oldPrice' => number_format($currentPrice, 2),
                        'newPrice' => number_format($newPrice, 2),
                        'change' => ($priceChange > 0 ? '+' : '') . round($priceChangePercent, 1) . '%',
                        'reason' => $pricingReason
                    ],
                    'selectionCriteria' => [
                        'marginTarget' => 30,
                        'activeFactors' => ['Margin Deviation', 'Inventory Level', 'Sales Performance']
                    ],
                    'strategies' => [
                        ['key' => strtolower(str_replace(' ', '-', $strategy)), 'range' => $this->getStrategyRange($strategy)]
                    ]
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Pricing bot error: ' . $e->getMessage(),
                'action' => 'error',
                'reason' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Intelligent product selection for pricing optimization
     */
    private function selectProductForPricing($products)
    {
        $scoredProducts = [];

        foreach ($products as $product) {
            $score = 0;
            $reasons = [];

            // Current margin analysis
            $currentPrice = (float) $product->selling_price;
            $costPrice = (float) $product->purchase_price;

            if ($costPrice > 0 && $currentPrice > 0) {
                $currentMargin = (($currentPrice - $costPrice) / $currentPrice) * 100;

                // Score based on margin deviation from target (25-35%)
                if ($currentMargin < 15) {
                    $score += 30; // High priority for low margin
                    $reasons[] = "Low margin (" . round($currentMargin, 1) . "%)";
                } elseif ($currentMargin > 50) {
                    $score += 20; // High priority for excessive margin
                    $reasons[] = "Excessive margin (" . round($currentMargin, 1) . "%)";
                } elseif ($currentMargin < 25 || $currentMargin > 35) {
                    $score += 15; // Medium priority for off-target margin
                    $reasons[] = "Off-target margin (" . round($currentMargin, 1) . "%)";
                }

                // Inventory level influence
                $stockQuantity = (int) ($product->stock_quantity ?? 0);
                if ($stockQuantity == 0) {
                    $score += 25; // High priority for out-of-stock (clear inventory)
                    $reasons[] = "Out of stock (liquidation pricing)";
                } elseif ($stockQuantity < 10) {
                    $score += 15; // Medium priority for low stock
                    $reasons[] = "Low stock ({$stockQuantity} units)";
                } elseif ($stockQuantity > 100) {
                    $score += 10; // Lower priority for overstocked
                    $reasons[] = "Overstocked ({$stockQuantity} units)";
                }

                // Sales performance (if available)
                $totalSold = (int) ($product->total_sold ?? 0);
                if ($totalSold == 0) {
                    $score += 20; // High priority for non-moving inventory
                    $reasons[] = "No sales history";
                } elseif ($totalSold < 5) {
                    $score += 15; // Medium priority for slow movers
                    $reasons[] = "Slow moving ({$totalSold} sold)";
                } elseif ($totalSold > 50) {
                    $score += 10; // Lower priority for fast movers
                    $reasons[] = "Fast moving ({$totalSold} sold)";
                }

                // Price competitiveness (random factor for market positioning)
                if (rand(1, 100) <= 30) { // 30% chance
                    $score += 10;
                    $reasons[] = "Market competition analysis";
                }

                $scoredProducts[] = [
                    'product' => $product,
                    'score' => $score,
                    'reasons' => $reasons,
                    'margin' => $currentMargin
                ];
            }
        }

        // Sort by score (highest first)
        usort($scoredProducts, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        if (empty($scoredProducts)) {
            // Fallback to random selection
            $selectedProduct = $products[array_rand($products)];
            return [
                'product' => $selectedProduct,
                'reason' => 'Random selection (no scoring criteria met)'
            ];
        }

        // Select from top 3 candidates (adds some randomness)
        $topCandidates = array_slice($scoredProducts, 0, min(3, count($scoredProducts)));
        $selected = $topCandidates[array_rand($topCandidates)];

        $reasonText = !empty($selected['reasons'])
            ? implode(', ', $selected['reasons'])
            : 'Selected for optimization';

        return [
            'product' => $selected['product'],
            'reason' => $reasonText . " (Score: {$selected['score']})"
        ];
    }

    /**
     * Get dynamic margin targets based on product cost price
     * Low cost items can have high margins, high cost items should have lower margins
     */
    private function getDynamicMarginTarget($costPrice)
    {
        // Price-based margin strategy:
        // Low cost items ($0-$10): Can afford high margins (200-500%)
        // Medium-low cost ($10-$50): High margins (100-200%)
        // Medium cost ($50-$200): Moderate margins (50-100%)
        // Medium-high cost ($200-$1000): Lower margins (20-50%)
        // High cost ($1000+): Competitive margins (5-25%), can even sell at loss for strategic reasons

        if ($costPrice <= 10) {
            return [
                'min' => 200,
                'max' => 500,
                'category' => 'Low Cost - High Margin',
                'reasoning' => 'Small items can bear high markup for profitability'
            ];
        } elseif ($costPrice <= 50) {
            return [
                'min' => 100,
                'max' => 200,
                'category' => 'Medium-Low Cost - High Margin',
                'reasoning' => 'Accessories and small tools with good margin potential'
            ];
        } elseif ($costPrice <= 200) {
            return [
                'min' => 50,
                'max' => 100,
                'category' => 'Medium Cost - Moderate Margin',
                'reasoning' => 'Standard tools with balanced pricing'
            ];
        } elseif ($costPrice <= 1000) {
            return [
                'min' => 20,
                'max' => 50,
                'category' => 'Medium-High Cost - Lower Margin',
                'reasoning' => 'Expensive tools need competitive pricing'
            ];
        } else {
            return [
                'min' => 5,
                'max' => 25,
                'category' => 'High Cost - Competitive Margin',
                'reasoning' => 'Premium equipment sold at competitive rates, strategic loss acceptable'
            ];
        }
    }

    /**
     * Calculate smart pricing based on multiple strategies with dynamic margin targets
     */
    private function calculateSmartPrice($product, $currentPrice, $costPrice)
    {
        $currentMargin = (($currentPrice - $costPrice) / $currentPrice) * 100;
        $stockQuantity = (int) ($product->stock_quantity ?? 0);
        $totalSold = (int) ($product->total_sold ?? 0);

        // Get dynamic margin target based on cost price
        $marginTarget = $this->getDynamicMarginTarget($costPrice);
        $minMargin = $marginTarget['min'];
        $maxMargin = $marginTarget['max'];
        $category = $marginTarget['category'];

        // Strategy 1: Low margin products - increase to appropriate margin for price range
        if ($currentMargin < $minMargin) {
            $targetMarginPercent = rand($minMargin, $maxMargin);

            // Handle high margins (>100%) differently to avoid division by zero
            if ($targetMarginPercent >= 100) {
                $markup = $targetMarginPercent / 100;
                $newPrice = $costPrice * (1 + $markup);
            } else {
                $targetMargin = $targetMarginPercent / 100;
                $newPrice = $costPrice / (1 - $targetMargin);
            }

            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Low Margin Correction',
                'reason' => "Increasing margin from " . round($currentMargin, 1) . "% to " . $category . " range (" . $minMargin . "-" . $maxMargin . "%) for cost $" . number_format($costPrice, 2)
            ];
        }

        // Strategy 2: Overstocked items - reduce margin for movement
        if ($stockQuantity > 100) {
            // For overstocked items, use 75% of minimum margin to encourage movement
            $clearanceMargin = max(5, round($minMargin * 0.75)); // Never go below 5%
            $targetMargin = $clearanceMargin / 100;
            $newPrice = $costPrice / (1 - $targetMargin);
            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Inventory Movement',
                'reason' => "Reducing margin to {$clearanceMargin}% to move excess inventory ({$stockQuantity} units) for " . $category
            ];
        }

        // Strategy 3: Out of stock - premium pricing within category limits
        if ($stockQuantity == 0) {
            // For out of stock, use maximum margin for the category
            if ($maxMargin >= 100) {
                $markup = $maxMargin / 100;
                $newPrice = $costPrice * (1 + $markup);
            } else {
                $targetMargin = $maxMargin / 100;
                $newPrice = $costPrice / (1 - $targetMargin);
            }

            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Scarcity Premium',
                'reason' => "Maximum margin pricing ({$maxMargin}%) for out-of-stock " . $category . " item"
            ];
        }

        // Strategy 4: Fast movers - optimize within category range
        if ($totalSold > 50) {
            // For fast movers, use upper range of category margin
            $fastMoverMargin = $minMargin + (($maxMargin - $minMargin) * 0.8); // 80% towards max

            if ($fastMoverMargin >= 100) {
                $markup = $fastMoverMargin / 100;
                $newPrice = $costPrice * (1 + $markup);
            } else {
                $targetMargin = $fastMoverMargin / 100;
                $newPrice = $costPrice / (1 - $targetMargin);
            }

            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Demand Optimization',
                'reason' => "High demand pricing (" . round($fastMoverMargin, 1) . "%) for popular " . $category . " item ({$totalSold} sold)"
            ];
        }

        // Strategy 5: Slow movers - clearance pricing below category minimum
        if ($totalSold < 5) {
            // For slow movers, go below minimum margin to clear inventory
            $clearanceMargin = max(5, round($minMargin * 0.5)); // 50% of minimum, never below 5%
            $targetMargin = $clearanceMargin / 100;
            $newPrice = $costPrice / (1 - $targetMargin);
            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Clearance Pricing',
                'reason' => "Clearance pricing ({$clearanceMargin}%) for slow-moving " . $category . " item ({$totalSold} sold)"
            ];
        }

        // Strategy 6: Excessive margin - bring down to category maximum
        if ($currentMargin > $maxMargin) {
            $targetMargin = rand($minMargin, $maxMargin) / 100;
            $newPrice = $costPrice / (1 - $targetMargin);
            return [
                'price' => round($newPrice, 2),
                'strategy' => 'Market Competitive',
                'reason' => "Reducing excessive margin (" . round($currentMargin, 1) . "%) to " . $category . " range ({$minMargin}-{$maxMargin}%)"
            ];
        }

        // Strategy 7: Strategic loss for high-cost items
        if ($costPrice > 1000 && $currentMargin > 0) {
            // For very expensive items, consider strategic loss to move inventory
            $strategicLoss = rand(-5, 5); // Can go negative for strategic pricing
            $targetMargin = $strategicLoss / 100;
            $newPrice = $costPrice / (1 - $targetMargin);

            if ($strategicLoss < 0) {
                return [
                    'price' => round($newPrice, 2),
                    'strategy' => 'Strategic Loss',
                    'reason' => "Strategic loss pricing (" . $strategicLoss . "%) for high-cost item to compete and move inventory"
                ];
            }
        }

        // Default strategy: Optimize within category range
        $targetMarginValue = rand($minMargin, $maxMargin);
        $targetMargin = $targetMarginValue / 100;
        $newPrice = $costPrice / (1 - $targetMargin);

        return [
            'price' => round($newPrice, 2),
            'strategy' => 'Category Optimization',
            'reason' => "Optimizing to {$targetMarginValue}% margin for " . $category
        ];
    }

    /**
     * Get the price range for a given strategy
     */
    private function getStrategyRange($strategy)
    {
        switch ($strategy) {
            case 'Low Margin Correction':
                return 'Dynamic (5-500%)';
            case 'Inventory Movement':
                return '75% of category min';
            case 'Scarcity Premium':
                return 'Category maximum';
            case 'Demand Optimization':
                return '80% towards category max';
            case 'Clearance Pricing':
                return '50% of category min';
            case 'Market Competitive':
                return 'Category range';
            case 'Strategic Loss':
                return '-5% to +5%';
            case 'Category Optimization':
                return 'Full category range';
            default:
                return 'Dynamic';
        }
    }

    /**
     * Get pending purchase items that need receiving
     */
    private function getPendingPurchaseItems()
    {
        try {
            $this->db->query("
                SELECT 
                    pi.purchase_item_id,
                    pi.purchase_id,
                    pi.product_id,
                    pi.quantity,
                    pi.received_quantity,
                    pi.unit_price,
                    p.po_number,
                    p.status as purchase_status,
                    pr.product_name
                FROM purchase_items pi
                JOIN purchases p ON pi.purchase_id = p.purchase_id
                JOIN products pr ON pi.product_id = pr.product_id
                WHERE p.status IN ('pending', 'sent', 'in_transit', 'ready_to_receive')
                AND pi.quantity > COALESCE(pi.received_quantity, 0)
                ORDER BY p.purchase_date ASC
                LIMIT 20
            ");

            $this->db->execute();
            return $this->db->resultSet();

        } catch (Exception $e) {
            error_log("Error getting pending purchase items: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Process receiving of a purchase item
     */
    private function processReceiving($item, $receiveQty)
    {
        try {
            $this->db->beginTransaction();

            // 1. Update purchase_items received_quantity
            $newReceivedQty = ($item->received_quantity ?? 0) + $receiveQty;

            $this->db->query("
                UPDATE purchase_items 
                SET received_quantity = ?, 
                    received_at = NOW(),
                    receiving_notes = CONCAT(COALESCE(receiving_notes, ''), 'Bot received ', ?, ' units on ', NOW(), '; ')
                WHERE purchase_item_id = ?
            ");
            $this->db->bind(1, $newReceivedQty);
            $this->db->bind(2, $receiveQty);
            $this->db->bind(3, $item->purchase_item_id);
            $this->db->execute();

            // 2. Insert receiving record
            $this->db->query("
                INSERT INTO receiving (purchase_id, status, received_date, created_by, notes)
                VALUES (?, 'received', CURDATE(), 'receiving_bot', ?)
            ");
            $this->db->bind(1, $item->purchase_id);
            $this->db->bind(2, "Bot received {$receiveQty} units of {$item->product_name}");
            $this->db->execute();

            // 3. Update inventory
            $this->updateInventoryStock($item->product_id, $receiveQty);

            // 4. Check if purchase is fully received and update status
            $this->checkAndUpdatePurchaseStatus($item->purchase_id);

            $this->db->commit();

            return ['success' => true, 'message' => 'Receiving processed successfully'];

        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Check if all items in a purchase have been fully received and update status
     */
    private function checkAndUpdatePurchaseStatus($purchaseId)
    {
        try {
            // Check if all items are fully received
            $this->db->query("
                SELECT COUNT(*) as total_items,
                       SUM(CASE WHEN quantity <= COALESCE(received_quantity, 0) THEN 1 ELSE 0 END) as fully_received_items
                FROM purchase_items 
                WHERE purchase_id = ?
            ");
            $this->db->bind(1, $purchaseId);
            $this->db->execute();
            $result = $this->db->single();

            if ($result && $result->total_items == $result->fully_received_items) {
                // All items fully received - mark purchase as received
                $this->db->query("
                    UPDATE purchases 
                    SET status = 'received', received_at = NOW() 
                    WHERE purchase_id = ?
                ");
                $this->db->bind(1, $purchaseId);
                $this->db->execute();
            } else {
                // Partially received - update status if not already partially_received
                $this->db->query("
                    UPDATE purchases 
                    SET status = 'partially_received' 
                    WHERE purchase_id = ? AND status != 'partially_received'
                ");
                $this->db->bind(1, $purchaseId);
                $this->db->execute();
            }

        } catch (Exception $e) {
            // Log error but don't output to response
            error_log("Error updating purchase status: " . $e->getMessage());
        }
    }

    /**
     * Select product based on real-world sales frequency
     * Cheaper items should sell more often than expensive ones
     */
    private function selectProductByRealWorldFrequency($products)
    {
        // Categorize products by price with realistic frequency weights
        $highFrequency = []; // <$50  - 65% chance
        $mediumFrequency = []; // $50-$200 - 30% chance  
        $lowFrequency = []; // >$200 - 5% chance

        foreach ($products as $product) {
            $price = $product->selling_price ?? 50;

            if ($price <= 50) {
                $highFrequency[] = $product;
            } elseif ($price <= 200) {
                $mediumFrequency[] = $product;
            } else {
                $lowFrequency[] = $product;
            }
        }

        // Weighted random selection based on real-world buying patterns
        $random = rand(1, 100);

        if ($random <= 65 && !empty($highFrequency)) {
            // 65% chance - select from cheap items
            return $highFrequency[array_rand($highFrequency)];
        } elseif ($random <= 95 && !empty($mediumFrequency)) {
            // 30% chance (66-95) - select from mid-range items
            return $mediumFrequency[array_rand($mediumFrequency)];
        } elseif (!empty($lowFrequency)) {
            // 5% chance (96-100) - select from expensive items
            return $lowFrequency[array_rand($lowFrequency)];
        }

        // Fallback: if selected category is empty, pick from any available
        if (!empty($highFrequency)) {
            return $highFrequency[array_rand($highFrequency)];
        } elseif (!empty($mediumFrequency)) {
            return $mediumFrequency[array_rand($mediumFrequency)];
        } elseif (!empty($lowFrequency)) {
            return $lowFrequency[array_rand($lowFrequency)];
        }

        // Final fallback
        return $products[array_rand($products)];
    }

    /**
     * Public method for testing smart supplier selection
     * Only for testing purposes - exposes the private findOptimalSupplier method
     */
    public function testFindOptimalSupplier($productId, $quantity = 1, $urgency = 'normal')
    {
        return $this->findOptimalSupplier($productId, $quantity, $urgency);
    }
}
?>