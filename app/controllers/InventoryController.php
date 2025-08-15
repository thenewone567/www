<?php
/**
 * Inventory Controller
 * Handles inventory management operations
 */
class InventoryController extends Controller
{
    public $inventoryModel;
    public $productModel;
    public $categoryModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->inventoryModel = $this->model('Inventory');
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Enhanced inventory overview page
     */
    public function index()
    {
        // Get all products with inventory data
        $products = $this->productModel->getProducts();

        // Get categories for filtering
        $categories = [];
        try {
            $categories = $this->categoryModel->getCategories();
        } catch (Exception $e) {
            // If category model fails, continue with empty categories
            $categories = [];
        }

        // Get inventory summary
        $summary = $this->inventoryModel->getInventorySummary();
        if (!$summary) {
            $summary = (object) [
                'total_products' => count($products),
                'total_inventory_quantity' => 0,
                'total_inventory_value' => 0,
                'low_inventory_items' => 0
            ];
        }

        $data = [
            'title' => 'Enhanced Inventory Management',
            'products' => $products,
            'categories' => $categories,
            'summary' => $summary
        ];

        $this->view('inventory/index', $data);
    }

    /**
     * Inventory adjustments page
     */
    public function adjustments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'product_id' => trim($_POST['product_id']),
                'quantity_change' => trim($_POST['quantity_change']),
                'reason' => trim($_POST['reason']),
                'product_id_err' => '',
                'quantity_change_err' => '',
                'reason_err' => ''
            ];

            // Validate inputs
            if (empty($data['product_id'])) {
                $data['product_id_err'] = 'Please select a product';
            }

            if (empty($data['quantity_change'])) {
                $data['quantity_change_err'] = 'Please enter quantity change';
            } elseif (!is_numeric($data['quantity_change'])) {
                $data['quantity_change_err'] = 'Quantity must be a number';
            }

            if (empty($data['reason'])) {
                $data['reason_err'] = 'Please enter a reason for adjustment';
            }

            // Check if this is an AJAX request
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            // If no errors, process adjustment
            if (empty($data['product_id_err']) && empty($data['quantity_change_err']) && empty($data['reason_err'])) {
                // Calculate new inventory level based on adjustment type
                $currentInventory = $this->productModel->getCurrentInventory($data['product_id']);
                $quantityChange = (int) $data['quantity_change'];
                $newInventory = $currentInventory + $quantityChange;

                if ($newInventory < 0) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Cannot reduce inventory below zero. Current inventory: ' . $currentInventory]);
                        exit;
                    } else {
                        die('Cannot reduce inventory below zero');
                    }
                }

                // Use the Product model's adjustInventory method
                if ($this->productModel->adjustInventory($data['product_id'], $newInventory, $data['reason'], $_SESSION['user_id'] ?? null)) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => 'Inventory adjustment recorded successfully']);
                        exit;
                    } else {
                        flash('inventory_message', 'Inventory adjustment recorded successfully');
                        redirect('inventory/adjustments');
                    }
                } else {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Error processing inventory adjustment. Please check the inventory levels and try again.']);
                        exit;
                    } else {
                        die('Something went wrong');
                    }
                }
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'errors' => [
                            'product_id' => $data['product_id_err'],
                            'quantity_change' => $data['quantity_change_err'],
                            'reason' => $data['reason_err']
                        ]
                    ]);
                    exit;
                } else {
                    $data['products'] = $this->productModel->getProducts();
                    $this->view('inventory/adjustments', $data);
                }
            }
        } else {
            // Get products and recent adjustments
            $products = $this->productModel->getProducts();
            $adjustments = $this->inventoryModel->getInventoryMovements();

            $data = [
                'title' => 'Inventory Adjustments',
                'products' => $products,
                'adjustments' => $adjustments,
                'product_id' => '',
                'quantity_change' => '',
                'reason' => '',
                'product_id_err' => '',
                'quantity_change_err' => '',
                'reason_err' => ''
            ];

            $this->view('inventory/adjustments', $data);
        }
    }

    /**
     * Inventory movements page
     */
    public function movements()
    {
        $movements = $this->inventoryModel->getInventoryMovements();
        if (!$movements) {
            $movements = [];
            flash('inventory_message', 'No inventory movements found');
        }

        $data = [
            'title' => 'Inventory Movements',
            'movements' => $movements
        ];

        $this->view('inventory/movements', $data);
    }

    /**
     * Low inventory report
     */
    public function lowinventory()
    {
        $lowInventoryProducts = $this->inventoryModel->getLowInventoryItems();
        if (!$lowInventoryProducts) {
            $lowInventoryProducts = [];
            flash('inventory_message', 'No low inventory items found');
        }

        $data = [
            'title' => 'Low Inventory Report',
            'low_inventory_products' => $lowInventoryProducts
        ];

        $this->view('inventory/lowinventory', $data);
    }

    /**
     * Barcode scanning functionality
     */
    public function scanBarcode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = trim($_POST['barcode'] ?? '');

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode is required']);
                return;
            }

            $product = $this->inventoryModel->getProductByBarcode($barcode);

            if ($product) {
                echo json_encode([
                    'success' => true,
                    'product' => $product,
                    'message' => 'Product found'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            return;
        }

        // Show barcode scanner page
        $data = [
            'title' => 'Barcode Scanner'
        ];
        $this->view('inventory/scanner', $data);
    }

    /**
     * Enhanced Product Search for Unified Smart Search
     */
    public function search_products()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $query = trim($_POST['query'] ?? '');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        try {
            $products = $this->inventoryModel->searchProducts($query);

            echo json_encode([
                'success' => true,
                'results' => $products,
                'count' => count($products),
                'message' => count($products) . ' products found'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Enhanced Location Search for Unified Smart Search
     */
    public function search_locations()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $query = trim($_POST['query'] ?? '');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        try {
            $locations = $this->inventoryModel->searchLocations($query);

            echo json_encode([
                'success' => true,
                'results' => $locations,
                'count' => count($locations),
                'message' => count($locations) . ' locations found'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search Inventory Adjustments
     */
    public function search_adjustments()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $query = trim($_POST['query'] ?? '');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        try {
            $adjustments = $this->inventoryModel->searchAdjustments($query);

            echo json_encode([
                'success' => true,
                'results' => $adjustments,
                'count' => count($adjustments),
                'message' => count($adjustments) . ' adjustments found'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search Cycle Counts
     */
    public function search_cycle_counts()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $query = trim($_POST['query'] ?? '');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Search query is required']);
            return;
        }

        try {
            $cycleCounts = $this->inventoryModel->searchCycleCounts($query);

            echo json_encode([
                'success' => true,
                'results' => $cycleCounts,
                'count' => count($cycleCounts),
                'message' => count($cycleCounts) . ' cycle counts found'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Smart Inventory Adjustment Interface
     */
    public function smart_adjustment()
    {
        $data = [
            'title' => 'Smart Inventory Adjustment',
        ];

        $this->view('inventory/smart-adjustment', $data);
    }

    /**
     * Process Smart Inventory Adjustment
     */
    public function processAdjustment()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $_POST = sanitizePost($_POST);

        $productId = trim($_POST['product_id'] ?? '');
        $adjustmentType = trim($_POST['adjustment_type'] ?? '');
        $quantity = intval($_POST['quantity'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        // Validation
        if (empty($productId) || empty($adjustmentType) || $quantity <= 0 || empty($reason)) {
            echo json_encode([
                'success' => false,
                'message' => 'All fields are required and quantity must be greater than 0'
            ]);
            return;
        }

        try {
            // Get current inventory
            $currentInventory = $this->productModel->getCurrentInventory($productId);

            if ($currentInventory === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found or inventory data unavailable'
                ]);
                return;
            }

            // Calculate new inventory based on adjustment type
            $newInventory = $currentInventory;

            switch ($adjustmentType) {
                case 'add':
                    $newInventory = $currentInventory + $quantity;
                    break;
                case 'remove':
                    $newInventory = max(0, $currentInventory - $quantity);
                    break;
                case 'set':
                    $newInventory = $quantity;
                    break;
                default:
                    echo json_encode([
                        'success' => false,
                        'message' => 'Invalid adjustment type'
                    ]);
                    return;
            }

            // Apply the adjustment using existing product model method
            $success = $this->productModel->adjustInventory(
                $productId,
                $newInventory,
                $reason,
                $_SESSION['user_id'] ?? null
            );

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Inventory adjustment applied successfully',
                    'data' => [
                        'old_inventory' => $currentInventory,
                        'new_inventory' => $newInventory,
                        'adjustment_type' => $adjustmentType,
                        'quantity' => $quantity
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to apply inventory adjustment'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error in processAdjustment: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Adjustment failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Smart Cycle Count Interface
     */
    public function smart_cycle_count()
    {
        $data = [
            'title' => 'Smart Cycle Count',
        ];

        $this->view('inventory/smart-cycle-count', $data);
    }

    /**
     * Complete Cycle Count
     */
    public function completeCycleCount()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['count_info']) || !isset($input['counted_items'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data format']);
            return;
        }

        $countInfo = $input['count_info'];
        $countedItems = $input['counted_items'];

        if (empty($countedItems)) {
            echo json_encode(['success' => false, 'message' => 'No items to process']);
            return;
        }

        try {
            // Use the inventory model to save cycle count
            $result = $this->inventoryModel->saveCycleCount($countInfo, $countedItems, $_SESSION['user_id'] ?? null);

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cycle count completed successfully',
                    'data' => $result['data']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }

        } catch (Exception $e) {
            error_log("Error in completeCycleCount: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to complete cycle count: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update minimum inventory levels
     */
    public function updateMinimumInventory()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            $minimumInventory = trim($_POST['minimum_inventory'] ?? '');

            if (empty($productId) || empty($minimumInventory)) {
                flash('inventory_message', 'Product ID and minimum inventory are required', 'alert alert-danger');
                redirect('inventory/lowinventory');
            }

            if ($this->inventoryModel->updateMinimumInventory($productId, $minimumInventory)) {
                flash('inventory_message', 'Minimum inventory level updated successfully', 'alert alert-success');
            } else {
                flash('inventory_message', 'Failed to update minimum inventory level', 'alert alert-danger');
            }

            redirect('inventory/lowinventory');
        }
    }

    /**
     * Bulk transfer page - redirected to Import System
     * The bulk transfer functionality has been replaced with the comprehensive Import System
     */
    public function bulk_transfer()
    {
        // Redirect to the new Import System which handles bulk operations
        redirect('import');
    }

    /**
     * Process bulk transfer - redirected to Import System
     */
    public function process_bulk_transfer()
    {
        // Redirect to the new Import System which handles bulk operations
        redirect('import');
    }

    /**
     * Show inventory levels page
     */
    public function inventory_levels()
    {
        // Get inventory data
        $inventory = $this->inventoryModel->getAllinventory();
        if (!$inventory) {
            $inventory = [];
            flash('inventory_message', 'No inventory found');
        }

        // Get inventory movements
        $movements = $this->inventoryModel->getInventoryMovements();
        if (!$movements) {
            $movements = [];
            flash('inventory_message', 'No inventory movements found');
        }

        // Get warehouse locations
        $locations = $this->inventoryModel->getWarehouseLocations();
        if (!$locations) {
            $locations = [];
            flash('inventory_message', 'No warehouse locations found');
        }

        $data = [
            'title' => 'Inventory Levels',
            'inventory' => $inventory,
            'movements' => $movements,
            'locations' => $locations
        ];

        $this->view('inventory/inventory_levels', $data);
    }

    /**
     * Add inventory functionality
     */
    public function add_inventory()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'product_id' => isset($_POST['product_id']) ? trim($_POST['product_id']) : '',
                'batch_number' => isset($_POST['batch_number']) ? trim($_POST['batch_number']) : '',
                'expiry_date' => isset($_POST['expiry_date']) ? trim($_POST['expiry_date']) : '',
                'quantity' => isset($_POST['quantity']) ? trim($_POST['quantity']) : '',
                'location_id' => isset($_POST['location_id']) ? trim($_POST['location_id']) : '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];

            // Validate product id
            if (empty($data['product_id'])) {
                $data['product_id_err'] = 'Please enter product id';
            }
            // Validate quantity
            if (empty($data['quantity'])) {
                $data['quantity_err'] = 'Please enter quantity';
            } elseif (!is_numeric($data['quantity'])) {
                $data['quantity_err'] = 'Quantity must be a number';
            }

            if (empty($data['product_id_err']) && empty($data['quantity_err'])) {
                if ($this->inventoryModel->addInventory($data)) {
                    flash('inventory_message', 'Inventory Added', 'alert alert-success');
                    redirect('inventory/inventory_levels');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('inventory/add_inventory', $data);
            }
        } else {
            $data = [
                'title' => 'Add Inventory',
                'product_id' => '',
                'batch_number' => '',
                'expiry_date' => '',
                'quantity' => '',
                'location_id' => '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];
            $this->view('inventory/add_inventory', $data);
        }
    }

    /**
     * Legacy method for backward compatibility
     */

    /**
     * Move inventory between locations
     */
    public function move_inventory()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'product_id' => isset($_POST['product_id']) ? trim($_POST['product_id']) : '',
                'from_location_id' => isset($_POST['from_location_id']) ? trim($_POST['from_location_id']) : '',
                'to_location_id' => isset($_POST['to_location_id']) ? trim($_POST['to_location_id']) : '',
                'quantity' => isset($_POST['quantity']) ? trim($_POST['quantity']) : '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];

            // Validate product id
            if (empty($data['product_id'])) {
                $data['product_id_err'] = 'Please enter product id';
            }
            // Validate quantity
            if (empty($data['quantity'])) {
                $data['quantity_err'] = 'Please enter quantity';
            } elseif (!is_numeric($data['quantity'])) {
                $data['quantity_err'] = 'Quantity must be a number';
            }

            if (empty($data['product_id_err']) && empty($data['quantity_err'])) {
                if ($this->inventoryModel->addmove_inventory($data)) {
                    flash('inventory_message', 'Inventory Moved Successfully', 'alert alert-success');
                    redirect('inventory/inventory_levels');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('inventory/move_inventory', $data);
            }
        } else {
            $data = [
                'title' => 'Move Inventory',
                'product_id' => '',
                'from_location_id' => '',
                'to_location_id' => '',
                'quantity' => '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];
            $this->view('inventory/move_inventory', $data);
        }
    }

    /**
     * Warehouse location management
     */
    public function locations()
    {
        try {
            require_once APPROOT . DS . 'app' . DS . 'Database.php';
            $db = new Database();

            // Get all locations
            $db->query("SELECT * FROM locations ORDER BY location_type, standardized_address, location_code");
            $db->execute();
            $locations = $db->resultSet() ?? [];

            // Group locations by type
            $locationsByType = [];
            foreach ($locations as $location) {
                $locationsByType[$location->location_type][] = $location;
            }

            // Get location statistics
            $db->query("SELECT 
                       COUNT(*) as total_locations,
                       COUNT(CASE WHEN location_type = 'dock' THEN 1 END) as dock_locations,
                       COUNT(CASE WHEN location_type = 'receiving' THEN 1 END) as receiving_locations,
                       COUNT(CASE WHEN location_type = 'storage' THEN 1 END) as storage_locations,
                       COUNT(CASE WHEN location_type = 'bin' THEN 1 END) as bin_locations
                       FROM locations WHERE is_active = 1");
            $db->execute();
            $stats = $db->single();

        } catch (Exception $e) {
            error_log("Error loading locations: " . $e->getMessage());
            $locations = [];
            $locationsByType = [];
            $stats = (object) [
                'total_locations' => 0,
                'dock_locations' => 0,
                'receiving_locations' => 0,
                'storage_locations' => 0,
                'bin_locations' => 0
            ];
            flash('inventory_message', 'Error loading locations: ' . $e->getMessage(), 'alert alert-danger');
        }

        $data = [
            'title' => 'Warehouse Locations',
            'locations' => $locations,
            'locationsByType' => $locationsByType,
            'stats' => $stats
        ];

        $this->view('inventory/locations', $data);
    }

    /**
     * Add new warehouse location
     */
    public function addLocation()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'location_code' => isset($_POST['location_code']) ? trim($_POST['location_code']) : '',
                'standardized_address' => isset($_POST['standardized_address']) ? trim($_POST['standardized_address']) : '',
                'location_name' => isset($_POST['location_name']) ? trim($_POST['location_name']) : '',
                'location_type' => isset($_POST['location_type']) ? trim($_POST['location_type']) : '',
                'zone' => isset($_POST['zone']) ? trim($_POST['zone']) : '',
                'aisle' => isset($_POST['aisle']) ? trim($_POST['aisle']) : '',
                'shelf' => isset($_POST['shelf']) ? trim($_POST['shelf']) : '',
                'bin' => $this->processBinField($_POST),
                'capacity_cubic_feet' => isset($_POST['capacity_cubic_feet']) ? floatval($_POST['capacity_cubic_feet']) : 0,
                'max_weight_kg' => isset($_POST['max_weight_kg']) ? floatval($_POST['max_weight_kg']) : 0,
                'climate_controlled' => isset($_POST['climate_controlled']) ? 1 : 0,
                'notes' => isset($_POST['notes']) ? trim($_POST['notes']) : '',
                'errors' => []
            ];

            // Basic validation
            if (empty($data['location_code'])) {
                $data['errors'][] = 'Location code is required';
            }
            if (empty($data['location_name'])) {
                $data['errors'][] = 'Location name is required';
            }
            if (empty($data['location_type'])) {
                $data['errors'][] = 'Location type is required';
            }

            if (empty($data['errors'])) {
                try {
                    require_once APPROOT . DS . 'app' . DS . 'Database.php';
                    $db = new Database();

                    // Check if location code already exists
                    $db->query("SELECT location_id FROM locations WHERE location_code = ?");
                    $db->bind(1, $data['location_code']);
                    $db->execute();

                    if ($db->single()) {
                        flash('inventory_message', 'Location code already exists', 'alert alert-danger');
                        redirect('inventory/locations');
                        return;
                    }

                    // Auto-generate standardized address if not provided
                    if (empty($data['standardized_address'])) {
                        $data['standardized_address'] = $this->generateStandardizedAddress($data);
                    }

                    // Insert new location
                    $db->query("INSERT INTO locations (location_code, standardized_address, location_name, location_type, zone, aisle, shelf, bin, capacity_cubic_feet, max_weight_kg, climate_controlled, notes) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $db->bind(1, $data['location_code']);
                    $db->bind(2, $data['standardized_address']);
                    $db->bind(3, $data['location_name']);
                    $db->bind(4, $data['location_type']);
                    $db->bind(5, $data['zone']);
                    $db->bind(6, $data['aisle']);
                    $db->bind(7, $data['shelf']);
                    $db->bind(8, $data['bin']);
                    $db->bind(9, $data['capacity_cubic_feet']);
                    $db->bind(10, $data['max_weight_kg']);
                    $db->bind(11, $data['climate_controlled']);
                    $db->bind(12, $data['notes']);

                    if ($db->execute()) {
                        flash('inventory_message', 'Location added successfully!', 'alert alert-success');
                    } else {
                        flash('inventory_message', 'Failed to add location', 'alert alert-danger');
                    }

                } catch (Exception $e) {
                    error_log("Error adding location: " . $e->getMessage());
                    flash('inventory_message', 'Error adding location: ' . $e->getMessage(), 'alert alert-danger');
                }
            } else {
                flash('inventory_message', implode(', ', $data['errors']), 'alert alert-danger');
            }

            redirect('inventory/locations');
        }
    }

    /**
     * Add a range of warehouse locations
     */
    public function addLocationRange()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Basic validation
                $requiredFields = ['start_location', 'end_location', 'location_type', 'zone'];
                $errors = [];

                foreach ($requiredFields as $field) {
                    if (empty(trim($_POST[$field] ?? ''))) {
                        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
                    }
                }

                if (!empty($errors)) {
                    flash('inventory_message', implode(', ', $errors), 'alert alert-danger');
                    redirect('inventory/locations');
                    return;
                }

                // Parse start and end locations
                $startLocation = trim($_POST['start_location']);
                $endLocation = trim($_POST['end_location']);

                $startParts = $this->parseLocationCode($startLocation);
                $endParts = $this->parseLocationCode($endLocation);

                if (!$startParts || !$endParts) {
                    flash('inventory_message', 'Invalid location format. Use format like S1-B1-A1', 'alert alert-danger');
                    redirect('inventory/locations');
                    return;
                }

                // Validate range logic
                if ($startParts['shop'] != $endParts['shop']) {
                    flash('inventory_message', 'Start and end locations must be in the same shop', 'alert alert-danger');
                    redirect('inventory/locations');
                    return;
                }

                // Generate locations in range
                $locations = $this->generateLocationRange($startParts, $endParts);

                if (empty($locations)) {
                    flash('inventory_message', 'No valid locations generated from range', 'alert alert-danger');
                    redirect('inventory/locations');
                    return;
                }

                // Check for safety limit
                if (count($locations) > 500) {
                    flash('inventory_message', 'Range too large. Maximum 500 locations allowed per batch.', 'alert alert-danger');
                    redirect('inventory/locations');
                    return;
                }

                // Prepare common data
                $commonData = [
                    'location_type' => trim($_POST['location_type']),
                    'zone' => trim($_POST['zone']),
                    'capacity_cubic_feet' => !empty($_POST['capacity_cubic_feet']) ? (float) $_POST['capacity_cubic_feet'] : 0,
                    'max_weight_kg' => !empty($_POST['max_weight_kg']) ? (float) $_POST['max_weight_kg'] : 0,
                    'climate_controlled' => isset($_POST['climate_controlled']) ? 1 : 0,
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                // Start transaction
                $db = new Database();
                $db->beginTransaction();

                $insertedCount = 0;
                $skippedCount = 0;

                foreach ($locations as $locationCode) {
                    // Check if location already exists
                    $db->query("SELECT location_id FROM locations WHERE location_code = ?");
                    $db->bind(1, $locationCode);
                    $existing = $db->single();

                    if ($existing) {
                        $skippedCount++;
                        continue;
                    }

                    // Parse location parts for individual location data
                    $parts = $this->parseLocationCode($locationCode);

                    // Create location data
                    $locationData = array_merge($commonData, [
                        'location_code' => $locationCode,
                        'location_name' => $this->generateLocationName($locationCode, $commonData['location_type']),
                        'aisle' => $parts['aisle'],
                        'shelf' => $parts['rack'],
                        'bin' => $parts['column'] . $parts['bin']
                    ]);

                    // Generate standardized address
                    $locationData['standardized_address'] = $this->generateStandardizedAddress($locationData);

                    // Insert location
                    $db->query("INSERT INTO locations (location_code, standardized_address, location_name, location_type, zone, aisle, shelf, bin, capacity_cubic_feet, max_weight_kg, climate_controlled, notes) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $db->bind(1, $locationData['location_code']);
                    $db->bind(2, $locationData['standardized_address']);
                    $db->bind(3, $locationData['location_name']);
                    $db->bind(4, $locationData['location_type']);
                    $db->bind(5, $locationData['zone']);
                    $db->bind(6, $locationData['aisle']);
                    $db->bind(7, $locationData['shelf']);
                    $db->bind(8, $locationData['bin']);
                    $db->bind(9, $locationData['capacity_cubic_feet']);
                    $db->bind(10, $locationData['max_weight_kg']);
                    $db->bind(11, $locationData['climate_controlled']);
                    $db->bind(12, $locationData['notes']);

                    if ($db->execute()) {
                        $insertedCount++;
                    } else {
                        // Rollback on error
                        $db->rollback();
                        flash('inventory_message', 'Failed to insert location: ' . $locationCode, 'alert alert-danger');
                        redirect('inventory/locations');
                        return;
                    }
                }

                // Commit transaction
                $db->commit();

                // Success message
                $message = "Successfully created {$insertedCount} locations";
                if ($skippedCount > 0) {
                    $message .= " ({$skippedCount} locations skipped - already exist)";
                }
                flash('inventory_message', $message, 'alert alert-success');

            } catch (Exception $e) {
                if (isset($db)) {
                    $db->rollback();
                }
                error_log("Error creating location range: " . $e->getMessage());
                flash('inventory_message', 'Error creating location range: ' . $e->getMessage(), 'alert alert-danger');
            }

            redirect('inventory/locations');
        }
    }

    /**
     * Parse location code into components
     * Example: S1-B15-C3 -> ['shop' => 1, 'aisle' => 'B', 'rack' => 15, 'column' => 'C', 'bin' => 3]
     */
    private function parseLocationCode($locationCode)
    {
        if (preg_match('/^S(\d+)-([A-Z])(\d+)-([A-Z])(\d+)$/', $locationCode, $matches)) {
            return [
                'shop' => (int) $matches[1],
                'aisle' => $matches[2],
                'rack' => (int) $matches[3],
                'column' => $matches[4],
                'bin' => (int) $matches[5]
            ];
        }
        return false;
    }

    /**
     * Generate array of location codes between start and end
     */
    private function generateLocationRange($start, $end)
    {
        $locations = [];

        // Convert letters to numbers for iteration
        $startAisleNum = ord($start['aisle']) - ord('A');
        $endAisleNum = ord($end['aisle']) - ord('A');
        $startColumnNum = ord($start['column']) - ord('A');
        $endColumnNum = ord($end['column']) - ord('A');

        for ($aisle = $startAisleNum; $aisle <= $endAisleNum; $aisle++) {
            $aisleLetter = chr($aisle + ord('A'));

            $startRack = ($aisle == $startAisleNum) ? $start['rack'] : 1;
            $endRack = ($aisle == $endAisleNum) ? $end['rack'] : 20; // Max rack 20

            for ($rack = $startRack; $rack <= $endRack; $rack++) {
                $startColumn = ($aisle == $startAisleNum && $rack == $start['rack']) ? $startColumnNum : 0;
                $endColumn = ($aisle == $endAisleNum && $rack == $end['rack']) ? $endColumnNum : 3; // Max column D (index 3)

                for ($column = $startColumn; $column <= $endColumn; $column++) {
                    $columnLetter = chr($column + ord('A'));

                    $startBin = ($aisle == $startAisleNum && $rack == $start['rack'] && $column == $startColumnNum) ? $start['bin'] : 1;
                    $endBin = ($aisle == $endAisleNum && $rack == $end['rack'] && $column == $endColumnNum) ? $end['bin'] : 5; // Max bin 5

                    for ($bin = $startBin; $bin <= $endBin; $bin++) {
                        $locationCode = "S{$start['shop']}-{$aisleLetter}{$rack}-{$columnLetter}{$bin}";
                        $locations[] = $locationCode;
                    }
                }
            }
        }

        return $locations;
    }

    /**
     * Generate location name based on code and type
     */
    private function generateLocationName($locationCode, $locationType)
    {
        $parts = $this->parseLocationCode($locationCode);
        if (!$parts) {
            return $locationCode;
        }

        switch ($locationType) {
            case 'storage':
            case 'bin':
                return "Storage {$locationCode}";
            case 'receiving':
                return "Receiving {$locationCode}";
            case 'dock':
                return "Dock {$locationCode}";
            default:
                return $locationCode;
        }
    }

    /**
     * Process bin field to combine column and number
     */
    private function processBinField($postData)
    {
        // Check if bin is already set (from hidden field)
        if (isset($postData['bin']) && !empty(trim($postData['bin']))) {
            return trim($postData['bin']);
        }

        // Combine bin_column and bin_number if available
        $column = isset($postData['bin_column']) ? trim($postData['bin_column']) : '';
        $number = isset($postData['bin_number']) ? trim($postData['bin_number']) : '';

        if (!empty($column) && !empty($number)) {
            return $column . $number;
        }

        return '';
    }

    /**
     * Generate standardized address from location data
     * Format: S1-B15-C3 (Shop-AisleRack-ColumnBin)
     */
    private function generateStandardizedAddress($data)
    {
        switch ($data['location_type']) {
            case 'dock':
                // Format: "Dock-1", "Dock-2", "Dock-3"
                // Extract number from location code or use default
                if (preg_match('/([A-Z])$/i', $data['location_code'], $matches)) {
                    $dockNumber = ord(strtoupper($matches[1])) - ord('A') + 1;
                    return "Dock-{$dockNumber}";
                }
                return "Dock-1";

            case 'receiving':
                // Format: "RCV-01", "RCV-02", "RCV-03"
                if (preg_match('/(\d+)/', $data['location_code'], $matches)) {
                    return "RCV-" . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                }
                return "RCV-01";

            case 'storage':
            case 'bin':
                // Format: "S1-B15-C3" (Shop-AisleRack-ColumnBin)
                // S1 = Shop 1 (highest level)
                // B15 = Aisle B, Rack 15 (B1-B20: 20 racks total)
                // C3 = Column C, Bin 3 (Columns A-D, Bins 1-5)

                $shop = 1; // Default to Shop 1

                // Get aisle letter (A-Z) - default to B
                $aisleInput = !empty($data['aisle']) ? strtoupper(trim($data['aisle'])) : 'B';
                $aisleLetter = preg_match('/([A-Z])/', $aisleInput, $matches) ? $matches[1] : 'B';

                // Get rack number (1-20) - default to 1
                $rackInput = !empty($data['shelf']) ? trim($data['shelf']) : '1';
                $rackNumber = preg_match('/(\d+)/', $rackInput, $matches) ? (int) $matches[1] : 1;
                $rackNumber = max(1, min(20, $rackNumber)); // Ensure 1-20 range

                // Get column letter (A-D) - default to A
                $columnInput = !empty($data['bin']) ? strtoupper(trim($data['bin'])) : 'A';
                $columnLetter = preg_match('/([A-D])/', $columnInput, $matches) ? $matches[1] : 'A';

                // Get bin number (1-5) - extract from bin field or default to 1
                $binNumber = 1;
                if (!empty($data['bin'])) {
                    if (preg_match('/(\d+)/', $data['bin'], $matches)) {
                        $binNumber = max(1, min(5, (int) $matches[1])); // Ensure 1-5 range
                    }
                }

                return "S{$shop}-{$aisleLetter}{$rackNumber}-{$columnLetter}{$binNumber}";

            default:
                return "S1-B1-A1";
        }
    }

    /**
     * Generate barcode for warehouse location
     */
    public function generate_location_barcode($location_id = null)
    {
        if (!$location_id || !is_numeric($location_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid location ID']);
            exit;
        }

        $barcodeModel = $this->model('Barcode');

        // Check if location exists
        $locations = $this->inventoryModel->getWarehouseLocations();
        $location = null;
        foreach ($locations as $loc) {
            if ($loc->location_id == $location_id) {
                $location = $loc;
                break;
            }
        }

        if (!$location) {
            echo json_encode(['success' => false, 'message' => 'Location not found']);
            exit;
        }

        // Check if barcode already exists
        $existingBarcode = $barcodeModel->getBarcodesForLocation($location_id);
        if ($existingBarcode) {
            echo json_encode([
                'success' => false,
                'message' => 'Location already has a barcode',
                'barcode' => $existingBarcode[0]->barcode_value
            ]);
            exit;
        }

        // Generate new barcode
        $barcodeValue = $barcodeModel->generateBarcodeForLocation($location_id);
        if ($barcodeValue) {
            echo json_encode([
                'success' => true,
                'message' => 'Location barcode generated successfully',
                'barcode' => $barcodeValue
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate barcode']);
        }
        exit;
    }

    /**
     * View location barcodes
     */
    public function location_barcodes()
    {
        $barcodeModel = $this->model('Barcode');

        $locations = $this->inventoryModel->getWarehouseLocations();
        $locationBarcodes = $barcodeModel->getAllLocationBarcodes();

        $data = [
            'title' => 'Location Barcodes',
            'locations' => $locations,
            'location_barcodes' => $locationBarcodes
        ];

        $this->view('inventory/location_barcodes', $data);
    }

    /**
     * Print location barcodes
     */
    public function print_location_barcodes($location_id = null)
    {
        $barcodeModel = $this->model('Barcode');

        if ($location_id && is_numeric($location_id)) {
            // Print specific location barcode
            $locations = $this->inventoryModel->getWarehouseLocations();
            $location = null;
            foreach ($locations as $loc) {
                if ($loc->location_id == $location_id) {
                    $location = $loc;
                    break;
                }
            }

            if (!$location) {
                flash('inventory_message', 'Location not found', 'alert alert-danger');
                redirect('inventory/location_barcodes');
                return;
            }

            $barcodes = $barcodeModel->getBarcodesForLocation($location_id);
            if (!$barcodes) {
                // Generate barcode if doesn't exist
                $barcodeValue = $barcodeModel->generateBarcodeForLocation($location_id);
                if ($barcodeValue) {
                    $barcodes = [['barcode_value' => $barcodeValue, 'type' => 'CODE128']];
                }
            }

            $data = [
                'title' => 'Print Location Barcode - ' . $location->location_name,
                'location' => $location,
                'barcodes' => $barcodes
            ];
        } else {
            // Print all location barcodes
            $locationBarcodes = $barcodeModel->getAllLocationBarcodes();

            $data = [
                'title' => 'Print All Location Barcodes',
                'location_barcodes' => $locationBarcodes
            ];
        }

        $this->view('inventory/print_location_barcodes', $data);
    }

    /**
     * Bulk generate barcodes for locations without barcodes
     */
    public function bulk_generate_location_barcodes()
    {
        $barcodeModel = $this->model('Barcode');

        $locations = $this->inventoryModel->getWarehouseLocations();
        $generatedCount = 0;
        $errors = [];

        foreach ($locations as $location) {
            $existingBarcode = $barcodeModel->getBarcodesForLocation($location->location_id);

            if (!$existingBarcode) {
                $barcodeValue = $barcodeModel->generateBarcodeForLocation($location->location_id);
                if ($barcodeValue) {
                    $generatedCount++;
                } else {
                    $errors[] = "Failed to generate barcode for " . $location->location_name;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'generated' => $generatedCount,
            'errors' => $errors,
            'message' => "$generatedCount location barcodes generated successfully"
        ]);
        exit;
    }

    /**
     * Scan location barcode for inventory management
     */
    public function scan_location_barcode()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $barcode = $_POST['barcode'] ?? '';

            if (empty($barcode)) {
                echo json_encode(['success' => false, 'message' => 'Barcode required']);
                exit;
            }

            $barcodeModel = $this->model('Barcode');
            $location = $barcodeModel->getLocationByBarcode($barcode);

            if ($location) {
                echo json_encode([
                    'success' => true,
                    'location' => [
                        'id' => $location->location_id,
                        'name' => $location->location_name,
                        'rack' => $location->rack,
                        'shelf' => $location->shelf,
                        'barcode' => $location->barcode_value
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Location not found']);
            }
        }
    }

    /**
     * Workflow Pages
     */
    public function receiving()
    {
        error_log("InventoryController::receiving() method called");

        // Get receiving statistics and recent activity
        $receivingStats = [
            'deliveries_today' => 0,
            'items_received_today' => 0,
            'pending_items' => 0,
            'completed_items' => 0
        ];

        $recentActivity = [];

        try {
            require_once APPROOT . DS . 'app' . DS . 'Database.php';
            $db = new Database();

            error_log("Starting receiving stats queries...");

            // Get today's received purchase orders count
            $db->query("SELECT COUNT(*) as count FROM purchases WHERE DATE(received_at) = CURDATE() AND status = 'received'");
            if (!$db->execute()) {
                error_log("Failed to execute deliveries today query");
            }
            $result = $db->single();
            $receivingStats['deliveries_today'] = $result->count ?? 0;
            error_log("Deliveries today: " . $receivingStats['deliveries_today']);

            // Get items received today (from purchase_items)
            $db->query("SELECT COALESCE(SUM(pi.quantity), 0) as count 
                            FROM purchase_items pi 
                            JOIN purchases p ON pi.purchase_id = p.purchase_id 
                            WHERE DATE(p.received_at) = CURDATE() AND p.status = 'received'");
            if (!$db->execute()) {
                error_log("Failed to execute items received query");
            }
            $result = $db->single();
            $receivingStats['items_received_today'] = $result->count ?? 0;

            // Get pending purchase orders (ready to receive)
            $db->query("SELECT COUNT(*) as count FROM purchases WHERE status IN ('ready_to_receive', 'receiving_in_progress')");
            if (!$db->execute()) {
                error_log("Failed to execute pending items query");
            }
            $result = $db->single();
            $receivingStats['pending_items'] = $result->count ?? 0;

            // Get completed items today (same as items received for now)
            $receivingStats['completed_items'] = $receivingStats['items_received_today'];

            // Get recent receiving activity from purchases
            $db->query("SELECT p.purchase_id, p.po_number, p.status, p.received_at, p.total_amount,
                                   s.supplier_name, p.created_by as receiver_name
                            FROM purchases p 
                            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                            WHERE p.status IN ('received', 'ready_to_receive', 'receiving_in_progress', 'partially_received', 'completed')
                            ORDER BY COALESCE(p.received_at, p.updated_at) DESC 
                            LIMIT 10");
            if (!$db->execute()) {
                error_log("Failed to execute recent activity query");
            }
            $recentActivity = $db->resultSet() ?? [];

            error_log("Receiving controller: Found " . count($recentActivity) . " recent activities");

        } catch (Exception $e) {
            error_log("Receiving stats error: " . $e->getMessage());
            // Keep default values if database query fails
        }

        $data = [
            'title' => 'Receiving Operations',
            'receivingStats' => $receivingStats,
            'recentActivity' => $recentActivity
        ];
        $this->view('inventory/receiving', $data);
    }

    public function putaway()
    {
        $data = [
            'title' => 'Putaway Operations'
        ];
        $this->view('inventory/putaway', $data);
    }

    public function replenishment()
    {
        $data = [
            'title' => 'Replenishment Operations'
        ];
        $this->view('inventory/replenishment', $data);
    }

    public function cycle_counting()
    {
        $data = [
            'title' => 'Cycle Counting Operations'
        ];
        $this->view('inventory/cycle-counting', $data);
    }

    public function transfers()
    {
        $data = [
            'title' => 'Transfer Operations'
        ];
        $this->view('inventory/transfers', $data);
    }

    /**
     * Get PO details with expected items for receiving
     */
    public function getPODetails($poNumber = null)
    {
        if (!$poNumber && isset($_POST['po_number'])) {
            $poNumber = $_POST['po_number'];
        }

        if (!$poNumber) {
            echo json_encode(['error' => 'PO number is required']);
            return;
        }

        try {
            require_once APPROOT . DS . 'app' . DS . 'Database.php';
            $db = new Database();

            // Get purchase order details
            $db->query("SELECT p.*, s.supplier_name 
                       FROM purchases p 
                       LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                       WHERE p.po_number = ?");
            $db->bind(1, $poNumber);
            $db->execute();
            $purchase = $db->single();

            if (!$purchase) {
                echo json_encode(['error' => 'Purchase order not found']);
                return;
            }

            // Get purchase items (expected products)
            $db->query("SELECT pi.*, pr.product_name, pr.sku, pr.barcode 
                       FROM purchase_items pi 
                       LEFT JOIN products pr ON pi.product_id = pr.product_id 
                       WHERE pi.purchase_id = ?
                       ORDER BY pr.product_name");
            $db->bind(1, $purchase->purchase_id);
            $db->execute();
            $items = $db->resultSet() ?? [];

            $response = [
                'success' => true,
                'po' => [
                    'po_number' => $purchase->po_number,
                    'supplier_name' => $purchase->supplier_name ?? 'Unknown Supplier',
                    'expected_date' => $purchase->expected_date,
                    'total_amount' => $purchase->total_amount,
                    'status' => $purchase->status,
                    'purchase_date' => $purchase->purchase_date,
                    'notes' => $purchase->notes ?? ''
                ],
                'items' => []
            ];

            foreach ($items as $item) {
                $response['items'][] = [
                    'purchase_item_id' => $item->purchase_item_id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?? 'Unknown Product',
                    'sku' => $item->sku ?? '',
                    'barcode' => $item->barcode ?? '',
                    'expected_quantity' => $item->quantity,
                    'unit_price' => $item->unit_price ?? 0,
                    'total_price' => ($item->quantity * ($item->unit_price ?? 0)),
                    'received_quantity' => $item->received_quantity ?? 0
                ];
            }

            echo json_encode($response);

        } catch (Exception $e) {
            error_log("Error fetching PO details: " . $e->getMessage());
            echo json_encode(['error' => 'Failed to load PO details']);
        }
    }

    /**
     * Process received item from purchase order
     */
    public function receiveItem()
    {
        header('Content-Type: application/json');

        try {
            // Log submission initiation
            error_log("Initiating submission - receiveItem process started");

            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                error_log("Submission failed! Details: Invalid input data");
                echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
                return;
            }

            $purchaseItemId = $input['purchase_item_id'] ?? null;
            $receivedQuantity = $input['received_quantity'] ?? 0;
            $condition = $input['condition'] ?? 'good';
            $dockLocation = $input['dock_location'] ?? '';
            $poNumber = $input['po_number'] ?? '';

            if (!$purchaseItemId || $receivedQuantity <= 0) {
                error_log("Submission failed! Details: Invalid purchase item ID or quantity");
                echo json_encode(['status' => 'error', 'message' => 'Invalid purchase item ID or quantity']);
                return;
            }

            if (!$dockLocation) {
                error_log("Submission failed! Details: Dock location is required");
                echo json_encode(['status' => 'error', 'message' => 'Dock location is required']);
                return;
            }

            $db = new Database();

            // Get purchase item details
            $db->query("SELECT pi.*, pr.product_name, pr.sku, p.po_number 
                       FROM purchase_items pi 
                       LEFT JOIN products pr ON pi.product_id = pr.product_id 
                       LEFT JOIN purchases p ON pi.purchase_id = p.purchase_id 
                       WHERE pi.purchase_item_id = ?");
            $db->bind(1, $purchaseItemId);
            $db->execute();
            $item = $db->single();

            if (!$item) {
                echo json_encode(['status' => 'error', 'message' => 'Purchase item not found']);
                return;
            }

            // Check if quantity exceeds expected
            if ($receivedQuantity > $item->quantity) {
                echo json_encode(['status' => 'error', 'message' => 'Received quantity exceeds expected quantity']);
                return;
            }

            // Begin transaction
            $db->beginTransaction();

            try {
                // Update inventory - add received items
                $db->query("INSERT INTO inventory (product_id, quantity, location, condition, received_date, po_number, notes) 
                           VALUES (?, ?, ?, ?, NOW(), ?, ?)");
                $db->bind(1, $item->product_id);
                $db->bind(2, $receivedQuantity);
                $db->bind(3, $dockLocation);
                $db->bind(4, $condition);
                $db->bind(5, $item->po_number);
                $db->bind(6, "Received from PO {$item->po_number} at {$dockLocation}");
                $db->execute();

                // Update purchase item with received quantity
                $db->query("UPDATE purchase_items 
                           SET received_quantity = COALESCE(received_quantity, 0) + ?, 
                               received_at = NOW(),
                               condition_notes = ?
                           WHERE purchase_item_id = ?");
                $db->bind(1, $receivedQuantity);
                $db->bind(2, $condition !== 'good' ? "Condition: {$condition}" : null);
                $db->bind(3, $purchaseItemId);
                $db->execute();

                // Check if PO is fully received and update status
                $db->query("SELECT COUNT(*) as total_items,
                           SUM(CASE WHEN received_quantity >= quantity THEN 1 ELSE 0 END) as completed_items
                           FROM purchase_items 
                           WHERE purchase_id = ?");
                $db->bind(1, $item->purchase_id);
                $db->execute();
                $poProgress = $db->single();

                if ($poProgress && $poProgress->total_items > 0 && $poProgress->completed_items >= $poProgress->total_items) {
                    // All items received - mark PO as fully received
                    $db->query("UPDATE purchases SET status = 'received', received_at = NOW() WHERE purchase_id = ?");
                    $db->bind(1, $item->purchase_id);
                    $db->execute();
                }

                // Log the transaction
                $db->query("INSERT INTO inventory_transactions (product_id, transaction_type, quantity, reference_type, reference_id, notes, created_at) 
                           VALUES (?, 'received', ?, 'purchase_order', ?, ?, NOW())");
                $db->bind(1, $item->product_id);
                $db->bind(2, $receivedQuantity);
                $db->bind(3, $item->purchase_id);
                $db->bind(4, "Received {$receivedQuantity} units from PO {$item->po_number}");
                $db->execute();

                $db->commit();

                // Log successful submission
                error_log("Submission successful! Received {$receivedQuantity} units of {$item->product_name}");

                echo json_encode([
                    'status' => 'success',
                    'message' => "Successfully received {$receivedQuantity} units of {$item->product_name}",
                    'item' => [
                        'purchase_item_id' => $purchaseItemId,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,
                        'received_quantity' => $receivedQuantity,
                        'condition' => $condition
                    ]
                ]);

            } catch (Exception $e) {
                $db->rollback();
                error_log("Submission failed! Details: " . $e->getMessage());
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Submission failed! Details: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Failed to process received item']);
        }
    }

    /**
     * Get dock and receiving locations for dropdown
     */
    public function getDockLocations()
    {
        header('Content-Type: application/json');

        try {
            require_once APPROOT . DS . 'app' . DS . 'Database.php';
            $db = new Database();

            // Get dock and receiving locations
            $db->query("SELECT location_code, location_name, location_type 
                       FROM locations 
                       WHERE location_type IN ('dock', 'receiving') 
                       AND is_active = 1 
                       ORDER BY location_type, location_code");
            $db->execute();
            $locations = $db->resultSet() ?? [];

            echo json_encode([
                'success' => true,
                'locations' => $locations
            ]);

        } catch (Exception $e) {
            error_log("Error loading dock locations: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load dock locations'
            ]);
        }
    }
}