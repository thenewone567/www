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
     * Bulk transfer page - move items from bulk locations to regular locations
     */
    public function bulk_transfer()
    {
        // Get items in bulk locations
        $bulkItems = $this->inventoryModel->getBulkLocationInventory();

        // Get regular (non-bulk) locations for transfer destinations
        $regularLocations = $this->inventoryModel->getRegularLocations();

        $data = [
            'title' => 'Bulk Location Transfer',
            'bulk_items' => $bulkItems,
            'regular_locations' => $regularLocations
        ];

        $this->view('inventory/bulk_transfer', $data);
    }

    /**
     * Process bulk transfer
     */
    public function process_bulk_transfer()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transfers = $_POST['transfers'] ?? [];
            $successCount = 0;
            $failCount = 0;

            foreach ($transfers as $inventoryId => $transferData) {
                if (!empty($transferData['quantity']) && !empty($transferData['to_location']) && $transferData['quantity'] > 0) {
                    $result = $this->inventoryModel->transferFromBulkLocation(
                        $inventoryId,
                        $transferData['quantity'],
                        $transferData['to_location']
                    );

                    if ($result) {
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                }
            }

            if ($successCount > 0) {
                flash('inventory_message', "Successfully transferred $successCount items from bulk locations", 'alert alert-success');
            }
            if ($failCount > 0) {
                flash('inventory_message', "Failed to transfer $failCount items", 'alert alert-warning');
            }

            redirect('inventory/bulk_transfer');
        }
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
        $locations = $this->inventoryModel->getWarehouseLocations();
        if (!$locations) {
            $locations = [];
            flash('inventory_message', 'No warehouse locations found');
        }

        $data = [
            'title' => 'Warehouse Locations',
            'locations' => $locations
        ];

        $this->view('inventory/locations', $data);
    }

    /**
     * Add new warehouse location
     */
    public function addlocation()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $data = [
                'location_name' => isset($_POST['location_name']) ? trim($_POST['location_name']) : '',
                'location_code' => isset($_POST['location_code']) ? trim($_POST['location_code']) : '',
                'rack' => isset($_POST['rack']) ? trim($_POST['rack']) : '',
                'shelf' => isset($_POST['shelf']) ? trim($_POST['shelf']) : '',
                'location_name_err' => '',
                'location_code_err' => ''
            ];

            // Basic validation
            if (empty($data['location_name'])) {
                $data['location_name_err'] = 'Please enter location name';
            }
            if (empty($data['location_code'])) {
                $data['location_code_err'] = 'Please enter location code';
            }

            if (empty($data['location_name_err']) && empty($data['location_code_err'])) {
                if ($this->inventoryModel->addWarehouseLocation($data)) {
                    flash('inventory_message', 'Location Added Successfully', 'alert alert-success');
                    redirect('inventory/locations');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('inventory/addlocation', $data);
            }
        } else {
            $data = [
                'title' => 'Add Warehouse Location',
                'location_name' => '',
                'location_code' => '',
                'rack' => '',
                'shelf' => '',
                'location_name_err' => '',
                'location_code_err' => ''
            ];
            $this->view('inventory/addlocation', $data);
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
}