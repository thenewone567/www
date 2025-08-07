<?php
class ReceivingController extends Controller
{
    public $productModel;
    public $purchaseModel;
    public $supplierModel;
    public $locationModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $this->purchaseModel = $this->model('Purchase');
        $this->productModel = $this->model('Product');
        $this->supplierModel = $this->model('Supplier');

        // Check if LocationModel exists, otherwise use a simple mock
        if (file_exists(APPROOT . '/app/models/Location.php')) {
            $this->locationModel = $this->model('Location');
        }
    }

    /**
     * Receiving dashboard/index page
     */
    public function index()
    {
        // Get receiving statistics
        $pendingCount = $this->getPendingReceiptsCount();
        $partialCount = $this->getPartialReceiptsCount();
        $completedTodayCount = $this->getCompletedTodayCount();
        $totalItemsWeek = $this->getTotalItemsReceivedThisWeek();

        // Get recent receiving activity
        $recentActivity = $this->getRecentReceivingActivity(10);

        $data = [
            'title' => 'Receiving Center',
            'pending_count' => $pendingCount,
            'partial_count' => $partialCount,
            'completed_count' => $completedTodayCount,
            'total_items' => $totalItemsWeek,
            'recent_activity' => $recentActivity
        ];

        $this->view('receiving/index', $data);
    }

    /**
     * Show pending receipts awaiting processing
     */
    public function pending()
    {
        // Get filters from query parameters
        $filters = [
            'supplier' => $_GET['supplier'] ?? '',
            'po_number' => $_GET['po_number'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Get pending purchases
        $pendingPurchases = $this->purchaseModel->getPurchases([
            'status' => ['pending', 'ordered']
        ]);

        // Apply additional filters
        if (!empty($filters['supplier'])) {
            $pendingPurchases = array_filter($pendingPurchases, function ($purchase) use ($filters) {
                return $purchase->supplier_id == $filters['supplier'];
            });
        }

        if (!empty($filters['po_number'])) {
            $pendingPurchases = array_filter($pendingPurchases, function ($purchase) use ($filters) {
                return stripos($purchase->purchase_number ?? $purchase->purchase_id, $filters['po_number']) !== false;
            });
        }

        // Get suppliers for filter dropdown
        $suppliers = $this->supplierModel->getSuppliers();

        // Pagination
        $perPage = $_GET['per_page'] ?? 25;
        $currentPage = $_GET['page'] ?? 1;
        $totalRecords = count($pendingPurchases);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedPurchases = array_slice($pendingPurchases, $offset, $perPage);

        $data = [
            'title' => 'Pending Receipts',
            'pending_purchases' => $paginatedPurchases,
            'suppliers' => $suppliers,
            'total_count' => $totalRecords,
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_records' => $totalRecords
            ],
            'filters' => $filters
        ];

        $this->view('receiving/pending', $data);
    }

    /**
     * Show partial receipts that need completion
     */
    public function partial()
    {
        // Get filters from query parameters
        $filters = [
            'supplier' => $_GET['supplier'] ?? '',
            'po_number' => $_GET['po_number'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Get partially received purchases
        $partialPurchases = $this->purchaseModel->getPurchases([
            'status' => ['partially_received']
        ]);

        // Apply additional filters
        if (!empty($filters['supplier'])) {
            $partialPurchases = array_filter($partialPurchases, function ($purchase) use ($filters) {
                return $purchase->supplier_id == $filters['supplier'];
            });
        }

        if (!empty($filters['po_number'])) {
            $partialPurchases = array_filter($partialPurchases, function ($purchase) use ($filters) {
                return stripos($purchase->purchase_number ?? $purchase->purchase_id, $filters['po_number']) !== false;
            });
        }

        // Get suppliers for filter dropdown
        $suppliers = $this->supplierModel->getSuppliers();

        // Pagination
        $perPage = $_GET['per_page'] ?? 25;
        $currentPage = $_GET['page'] ?? 1;
        $totalRecords = count($partialPurchases);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedPurchases = array_slice($partialPurchases, $offset, $perPage);

        $data = [
            'title' => 'Partial Receipts',
            'partial_purchases' => $paginatedPurchases,
            'suppliers' => $suppliers,
            'total_count' => $totalRecords,
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_records' => $totalRecords
            ],
            'filters' => $filters
        ];

        $this->view('receiving/partial', $data);
    }

    /**
     * Show completed receipts
     */
    public function completed()
    {
        // Get completed purchases
        $completedPurchases = $this->purchaseModel->getPurchases([
            'status' => ['received', 'completed']
        ]);

        $data = [
            'title' => 'Completed Receipts',
            'completed_purchases' => $completedPurchases
        ];

        $this->view('receiving/completed', $data);
    }

    /**
     * Show completed receipts for today only
     */
    public function completed_today()
    {
        // Get filters from query parameters
        $filters = [
            'supplier' => $_GET['supplier'] ?? '',
            'po_number' => $_GET['po_number'] ?? '',
            'received_by' => $_GET['received_by'] ?? ''
        ];

        // Get completed receipts for today
        $completedReceipts = $this->purchaseModel->getPurchases([
            'status' => ['received', 'completed'],
            'date_received' => date('Y-m-d')
        ]);

        // Apply additional filters
        if (!empty($filters['supplier'])) {
            $completedReceipts = array_filter($completedReceipts, function ($receipt) use ($filters) {
                return $receipt->supplier_id == $filters['supplier'];
            });
        }

        if (!empty($filters['po_number'])) {
            $completedReceipts = array_filter($completedReceipts, function ($receipt) use ($filters) {
                return stripos($receipt->purchase_number ?? $receipt->purchase_id, $filters['po_number']) !== false;
            });
        }

        // Get suppliers for filter dropdown
        $suppliers = $this->supplierModel->getSuppliers();
        $users = $this->getUsersWhoReceived();

        // Pagination
        $perPage = $_GET['per_page'] ?? 25;
        $currentPage = $_GET['page'] ?? 1;
        $totalRecords = count($completedReceipts);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedReceipts = array_slice($completedReceipts, $offset, $perPage);

        $data = [
            'title' => 'Completed Today - ' . date('M j, Y'),
            'completed_receipts' => $paginatedReceipts,
            'suppliers' => $suppliers,
            'users' => $users,
            'total_count' => $totalRecords,
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_records' => $totalRecords
            ],
            'filters' => $filters,
            'date_filter' => date('Y-m-d'),
            'show_date_message' => true
        ];

        $this->view('receiving/completed-today', $data);
    }

    /**
     * Show items received this week
     */
    public function items_received()
    {
        // Get filters from query parameters
        $filters = [
            'supplier' => $_GET['supplier'] ?? '',
            'product' => $_GET['product'] ?? '',
            'date_from' => $_GET['date_from'] ?? date('Y-m-d', strtotime('monday this week')),
            'date_to' => $_GET['date_to'] ?? date('Y-m-d')
        ];

        // Get received items data
        $receivedItems = $this->getReceivedItemsDetails($filters);

        // Get suppliers and products for filter dropdowns
        $suppliers = $this->supplierModel->getSuppliers();
        $products = $this->productModel->getProducts();

        // Calculate summary statistics
        $totalItems = array_sum(array_column($receivedItems, 'quantity_received'));
        $totalValue = array_sum(array_column($receivedItems, 'total_value'));
        $uniqueOrders = count(array_unique(array_column($receivedItems, 'purchase_id')));

        // Pagination
        $perPage = $_GET['per_page'] ?? 25;
        $currentPage = $_GET['page'] ?? 1;
        $totalRecords = count($receivedItems);
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedItems = array_slice($receivedItems, $offset, $perPage);

        $data = [
            'title' => 'Items Received - This Week',
            'received_items' => $paginatedItems,
            'suppliers' => $suppliers,
            'products' => $products,
            'total_count' => $totalRecords,
            'total_items' => $totalItems,
            'total_value' => $totalValue,
            'unique_orders' => $uniqueOrders,
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_records' => $totalRecords
            ],
            'filters' => $filters
        ];

        $this->view('receiving/items-received', $data);
    }

    /**
     * Helper function to get received items details
     */
    private function getReceivedItemsDetails($filters = [])
    {
        // Mock data for now - in real implementation, this would query the database
        // for purchase_order_items joined with purchases and suppliers
        return [
            (object)[
                'purchase_id' => '1',
                'purchase_number' => 'PO-001',
                'supplier_name' => 'Test Supplier',
                'product_name' => 'Test Product',
                'quantity_ordered' => 100,
                'quantity_received' => 80,
                'unit_price' => 10.00,
                'total_value' => 800.00,
                'received_date' => date('Y-m-d'),
                'status' => 'partially_received'
            ]
        ];
    }

    /**
     * Get helper statistics
     */
    private function getPendingReceiptsCount()
    {
        $pendingPurchases = $this->purchaseModel->getPurchases([
            'status' => ['pending', 'ordered']
        ]);
        return count($pendingPurchases);
    }

    private function getPartialReceiptsCount()
    {
        $partialPurchases = $this->purchaseModel->getPurchases([
            'status' => ['partially_received']
        ]);
        return count($partialPurchases);
    }

    private function getCompletedTodayCount()
    {
        $completedToday = $this->purchaseModel->getPurchases([
            'status' => ['received', 'completed'],
            'date_received' => date('Y-m-d')
        ]);
        return count($completedToday);
    }

    private function getTotalItemsReceivedThisWeek()
    {
        // Mock data - in real implementation, sum quantities from received items this week
        return 150;
    }

    private function getRecentReceivingActivity($limit = 10)
    {
        $recentPurchases = $this->purchaseModel->getPurchases([
            'limit' => $limit,
            'order_by' => 'updated_at DESC'
        ]);
        return $recentPurchases;
    }

    private function getUsersWhoReceived()
    {
        // Mock data - in real implementation, get users who have received items
        return [];
    }
}
?>