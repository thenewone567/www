<?php
/**
 * Inventory Controller
 * Handles inventory management operations
 */
class InventoryController extends Controller
{
    public $inventoryModel;
    public $productModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->inventoryModel = $this->model('Inventory');
        $this->productModel = $this->model('Product');
    }

    /**
     * Inventory overview page
     */
    public function index()
    {
        // Get inventory summary
        $summary = $this->inventoryModel->getInventorySummary();
        if (!$summary) {
            $summary = (object) ['total_products' => 0, 'total_stock_quantity' => 0, 'total_stock_value' => 0, 'low_stock_items' => 0];
        }

        // Get stock data
        $stock = $this->inventoryModel->getAllStock();
        if (!$stock) {
            $stock = [];
            flash('inventory_message', 'No stock found');
        }

        // Get low stock items
        $lowStock = $this->inventoryModel->getLowStockItems();
        if (!$lowStock) {
            $lowStock = [];
        }

        // Get recent stock movements
        $recentMovements = $this->inventoryModel->getStockMovements(10);
        if (!$recentMovements) {
            $recentMovements = [];
        }

        $data = [
            'title' => 'Inventory Management',
            'summary' => $summary,
            'stock' => $stock,
            'low_stock' => $lowStock,
            'recent_movements' => $recentMovements
        ];

        $this->view('inventory/index', $data);
    }

    /**
     * Stock adjustments page
     */
    public function adjustments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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

            // If no errors, process adjustment
            if (empty($data['product_id_err']) && empty($data['quantity_change_err']) && empty($data['reason_err'])) {
                if ($this->inventoryModel->adjustStock($data)) {
                    flash('inventory_message', 'Stock adjustment recorded successfully');
                    redirect('inventory/adjustments');
                } else {
                    die('Something went wrong');
                }
            } else {
                $data['products'] = $this->productModel->getProducts();
                $this->view('inventory/adjustments', $data);
            }
        } else {
            // Get products and recent adjustments
            $products = $this->productModel->getProducts();
            $adjustments = $this->inventoryModel->getStockMovements();

            $data = [
                'title' => 'Stock Adjustments',
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
     * Stock movements page
     */
    public function movements()
    {
        $movements = $this->inventoryModel->getStockMovements();
        if (!$movements) {
            $movements = [];
            flash('inventory_message', 'No stock movements found');
        }

        $data = [
            'title' => 'Stock Movements',
            'movements' => $movements
        ];

        $this->view('inventory/movements', $data);
    }

    /**
     * Low stock report
     */
    public function lowstock()
    {
        $lowStockProducts = $this->inventoryModel->getLowStockItems();
        if (!$lowStockProducts) {
            $lowStockProducts = [];
            flash('inventory_message', 'No low stock items found');
        }

        $data = [
            'title' => 'Low Stock Report',
            'low_stock_products' => $lowStockProducts
        ];

        $this->view('inventory/lowstock', $data);
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
     * Update minimum stock levels
     */
    public function updateMinimumStock()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = trim($_POST['product_id'] ?? '');
            $minimumStock = trim($_POST['minimum_stock'] ?? '');

            if (empty($productId) || empty($minimumStock)) {
                flash('inventory_message', 'Product ID and minimum stock are required', 'alert alert-danger');
                redirect('inventory/lowstock');
            }

            if ($this->inventoryModel->updateMinimumStock($productId, $minimumStock)) {
                flash('inventory_message', 'Minimum stock level updated successfully', 'alert alert-success');
            } else {
                flash('inventory_message', 'Failed to update minimum stock level', 'alert alert-danger');
            }

            redirect('inventory/lowstock');
        }
    }
}