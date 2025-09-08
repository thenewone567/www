<?php
class SuppliersController extends Controller
{
    private $supplierModel;
    private $productSupplierModel;
    private $productModel;

    // ...existing code...

    public function show($id)
    {
        $supplier = $this->supplierModel->getSupplierById($id);
        // Debug output
        error_log('SupplierController@show: id=' . print_r($id, true));
        error_log('SupplierController@show: supplier=' . print_r($supplier, true));

        // Ensure $supplier is either an object or null
        if (!is_object($supplier) && !is_null($supplier)) {
            error_log('SupplierController@show: Unexpected supplier type: ' . gettype($supplier));
            $supplier = null;
        }

        // Get supplier purchase history and statistics
        $purchaseHistory = [];
        $supplierStats = null;
        $supplierProducts = [];

        if ($supplier) {
            $purchaseHistory = $this->supplierModel->getSupplierPurchases($id, 100);
            $supplierStats = $this->supplierModel->getSupplierStats($id);

            // Get products linked to this supplier using the normalized multi-supplier method
            $supplierProducts = $this->productModel->getProductsBySupplier($id);
        }

        $data = [
            'supplier' => $supplier,
            'purchase_history' => $purchaseHistory ?: [],
            'supplier_stats' => $supplierStats,
            'supplier_products' => $supplierProducts ?: []
        ];
        parent::view('suppliers/view', $data);
    }
    // AJAX endpoint for live duplicate checking
    public function check_duplicate()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $field = $input['field'] ?? '';
        $value = trim($input['value'] ?? '');
        $exists = false;
        $message = '';
        if ($field === 'supplier_name') {
            if ($this->supplierModel->isSupplierNameExists($value)) {
                $exists = true;
                $message = 'Supplier name already exists';
            }
        } elseif ($field === 'email') {
            if ($this->supplierModel->isEmailExists($value)) {
                $exists = true;
                $message = 'Email address already exists';
            }
        } elseif ($field === 'gst_number') {
            if ($this->supplierModel->isGstNumberExists($value)) {
                $exists = true;
                $message = 'GST number already exists';
            }
        }
        echo json_encode(['exists' => $exists, 'message' => $message]);
        exit;
    }

    // AJAX endpoint for live search suggestions
    public function search_ajax()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $limit = isset($_GET['limit']) ? max(1, min(20, intval($_GET['limit']))) : 10;

        if (empty($query) || strlen($query) < 2) {
            echo json_encode(['suppliers' => []]);
            exit;
        }

        try {
            $suppliers = $this->supplierModel->searchSuppliers($query, $limit);

            // Format the response for autocomplete
            $formattedSuppliers = [];
            foreach ($suppliers as $supplier) {
                $formattedSuppliers[] = [
                    'id' => $supplier->supplier_id,
                    'name' => $supplier->supplier_name,
                    'contact_person' => $supplier->contact_person,
                    'email' => $supplier->email,
                    'phone' => $supplier->phone,
                    'status' => $supplier->status,
                    'delivery_days' => $supplier->default_delivery_days
                ];
            }

            echo json_encode([
                'suppliers' => $formattedSuppliers,
                'query' => $query,
                'count' => count($formattedSuppliers)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
        exit;
    }

    public function __construct()
    {
        // Temporarily commented out for testing
        // if (!isLoggedIn()) {
        //     redirect('users/login');
        // }
        $this->supplierModel = $this->model('Supplier');
        $this->productSupplierModel = $this->model('ProductSupplier');
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        // Handle search, filter, and sorting parameters
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $tier = isset($_GET['tier']) ? $_GET['tier'] : '';
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'supplier_name';
        $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = isset($_GET['per_page']) ? max(5, min(100, intval($_GET['per_page']))) : 25;

        $offset = ($page - 1) * $perPage;

        // Get filtered suppliers
        $suppliers = $this->supplierModel->getSuppliersFiltered($search, $status, $tier, $sortBy, $sortOrder, $perPage, $offset);
        $totalSuppliers = $this->supplierModel->getFilteredSuppliersCount($search, $status, $tier);

        if (!$suppliers) {
            $suppliers = [];
            if (!empty($search)) {
                flash('supplier_message', 'No suppliers found matching your search criteria', 'alert alert-info');
            } else {
                flash('supplier_message', 'No suppliers found');
            }
        }

        // Calculate pagination
        $totalPages = ceil($totalSuppliers / $perPage);
        $startRecord = $totalSuppliers > 0 ? $offset + 1 : 0;
        $endRecord = min($offset + $perPage, $totalSuppliers);

        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'per_page' => $perPage,
            'total_records' => $totalSuppliers,
            'start_record' => $startRecord,
            'end_record' => $endRecord,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'previous_page' => $page - 1,
            'next_page' => $page + 1
        ];

        // Get comprehensive analytics data
        $analytics = $this->supplierModel->getSupplierAnalytics();
        $deliveryStats = $this->supplierModel->getDeliveryStats();
        $tierStats = $this->supplierModel->getTierStats();
        $topPerformers = $this->supplierModel->getTopPerformers(5);
        $poorPerformers = $this->supplierModel->getPoorPerformers(5);
        $recentDeliveries = $this->supplierModel->getRecentDeliveries(10);

        $data = [
            'suppliers' => $suppliers,
            'total_suppliers' => $analytics['total_suppliers'],
            'active_suppliers' => $analytics['active_suppliers'],
            'avg_delivery_days' => $analytics['avg_delivery_days'],
            'avg_on_time_rate' => $analytics['avg_on_time_rate'],
            'gold_tier_suppliers' => $analytics['gold_tier_suppliers'],
            'total_order_value' => $analytics['total_order_value'],
            'delivery_stats' => $deliveryStats,
            'tier_stats' => $tierStats,
            'top_performers' => $topPerformers,
            'poor_performers' => $poorPerformers,
            'recent_deliveries' => $recentDeliveries,
            'pagination' => $pagination,
            'current_search' => $search,
            'current_status' => $status,
            'current_tier' => $tier,
            'current_sort' => $sortBy,
            'current_order' => $sortOrder
        ];
        parent::view('suppliers/index', $data);
    }

    // AJAX endpoint to refresh performance data
    public function refresh_performance()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $this->supplierModel->refreshAllPerformanceData();
                echo json_encode(['success' => true, 'message' => 'Performance data refreshed successfully']);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to refresh performance data']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
    }

    // Activate supplier
    public function activate($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->supplierModel->setSupplierStatus($id, 'active')) {
                flash('supplier_message', 'Supplier activated');
            } else {
                flash('supplier_message', 'Failed to activate supplier', 'alert alert-danger');
            }
            redirect('suppliers');
        } else {
            redirect('suppliers');
        }
    }

    // Deactivate supplier
    public function deactivate($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->supplierModel->setSupplierStatus($id, 'inactive')) {
                flash('supplier_message', 'Supplier deactivated');
            } else {
                flash('supplier_message', 'Failed to deactivate supplier', 'alert alert-danger');
            }
            redirect('suppliers');
        } else {
            redirect('suppliers');
        }
    }

    // Toggle supplier status (active/inactive)
    public function toggle_status($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $supplier = $this->supplierModel->getSupplierById($id);
            if ($supplier) {
                $newStatus = ($supplier->status === 'active') ? 'inactive' : 'active';
                if ($this->supplierModel->setSupplierStatus($id, $newStatus)) {
                    // Check if this is an AJAX request
                    if (
                        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
                    ) {
                        // Return JSON response for AJAX
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => true,
                            'status' => $newStatus,
                            'message' => 'Supplier status updated to ' . $newStatus
                        ]);
                        exit;
                    } else {
                        flash('supplier_message', 'Supplier status updated to ' . $newStatus, 'alert alert-success');
                    }
                } else {
                    if (
                        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
                    ) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Failed to update supplier status'
                        ]);
                        exit;
                    } else {
                        flash('supplier_message', 'Failed to update supplier status', 'alert alert-danger');
                    }
                }
            } else {
                if (
                    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
                ) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Supplier not found'
                    ]);
                    exit;
                } else {
                    flash('supplier_message', 'Supplier not found', 'alert alert-danger');
                }
            }
            redirect('suppliers');
        } else {
            redirect('suppliers');
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'supplier_name' => isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '',
                'contact_person' => isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'gst_number' => isset($_POST['gst_number']) ? trim($_POST['gst_number']) : '',
                'default_delivery_days' => isset($_POST['default_delivery_days']) ? intval($_POST['default_delivery_days']) : 7,
                'added_by' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            } elseif ($this->supplierModel->isSupplierNameExists($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Supplier name already exists';
            }

            // Validate email format if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            } elseif (!empty($data['email']) && $this->supplierModel->isEmailExists($data['email'])) {
                $data['email_err'] = 'Email address already exists';
            }

            // Validate GST number for duplicates if provided
            if (!empty($data['gst_number']) && $this->supplierModel->isGstNumberExists($data['gst_number'])) {
                $data['gst_number_err'] = 'GST number already exists';
            }

            // Validate delivery days
            if ($data['default_delivery_days'] < 1 || $data['default_delivery_days'] > 365) {
                $data['default_delivery_days'] = 7; // Reset to default if invalid
            }

            if (empty($data['supplier_name_err']) && empty($data['email_err']) && empty($data['gst_number_err'])) {
                if ($this->supplierModel->addSupplier($data)) {
                    flash('supplier_message', 'Supplier Added');
                    redirect('suppliers');
                } else {
                    die('Something went wrong');
                }
            } else {
                parent::view('suppliers/add', $data);
            }
        } else {
            $data = [
                'supplier_name' => '',
                'contact_person' => '',
                'phone' => '',
                'email' => '',
                'address' => '',
                'gst_number' => '',
                'default_delivery_days' => 7,
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
            ];
            parent::view('suppliers/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'id' => $id,
                'supplier_name' => isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '',
                'contact_person' => isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'gst_number' => isset($_POST['gst_number']) ? trim($_POST['gst_number']) : '',
                'default_delivery_days' => isset($_POST['default_delivery_days']) ? intval($_POST['default_delivery_days']) : 7,
                'preferred_payment_terms' => isset($_POST['preferred_payment_terms']) ? trim($_POST['preferred_payment_terms']) : 'Net 30',
                'credit_limit' => isset($_POST['credit_limit']) ? floatval($_POST['credit_limit']) : 0,
                'current_outstanding' => isset($_POST['current_outstanding']) ? floatval($_POST['current_outstanding']) : 0,
                'is_verified' => isset($_POST['is_verified']) ? 1 : 0,
                'verification_date' => isset($_POST['verification_date']) ? trim($_POST['verification_date']) : null,
                'notes' => isset($_POST['notes']) ? trim($_POST['notes']) : '',
                'updated_by' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            } elseif ($this->supplierModel->isSupplierNameExists($data['supplier_name'], $id)) {
                $data['supplier_name_err'] = 'Supplier name already exists';
            }

            // Validate email format if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            }

            // Validate GST number for duplicates if provided
            if (!empty($data['gst_number']) && $this->supplierModel->isGstNumberExists($data['gst_number'], $id)) {
                $data['gst_number_err'] = 'GST number already exists';
            }

            // Validate delivery days
            if ($data['default_delivery_days'] < 1 || $data['default_delivery_days'] > 365) {
                $data['default_delivery_days'] = 7; // Reset to default if invalid
            }

            if (empty($data['supplier_name_err']) && empty($data['email_err']) && empty($data['gst_number_err'])) {
                if ($this->supplierModel->updateSupplier($data)) {
                    flash('supplier_message', 'Supplier Updated');
                    redirect('suppliers');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load supplier data to preserve values in case of errors
                $supplier = $this->supplierModel->getSupplierById($id);
                $data['supplier'] = $supplier;
                parent::view('suppliers/edit', $data);
            }
        } else {
            $supplier = $this->supplierModel->getSupplierById($id);
            if ($supplier) {
                $data = [
                    'id' => $id,
                    'supplier' => $supplier,
                    'supplier_name' => $supplier->supplier_name,
                    'contact_person' => $supplier->contact_person ?? '',
                    'phone' => $supplier->phone ?? '',
                    'email' => $supplier->email ?? '',
                    'address' => $supplier->address ?? '',
                    'gst_number' => $supplier->gst_number ?? '',
                    'supplier_name_err' => '',
                    'email_err' => '',
                    'gst_number_err' => ''
                ];
            } else {
                $data = [
                    'id' => $id,
                    'supplier_name' => '',
                    'contact_person' => '',
                    'phone' => '',
                    'email' => '',
                    'address' => '',
                    'gst_number' => '',
                    'supplier_name_err' => '',
                    'email_err' => '',
                    'gst_number_err' => ''
                ];
                flash('supplier_message', 'Supplier not found');
            }
            parent::view('suppliers/edit', $data);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Handle AJAX request with JSON response
            header('Content-Type: application/json');

            try {
                if ($this->supplierModel->deleteSupplier($id)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Supplier archived successfully. All records and history have been preserved.'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => 'Failed to archive supplier. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Error archiving supplier: ' . $e->getMessage()
                ]);
            }
            exit;
        } else {
            redirect('suppliers');
        }
    }

    /**
     * Link Suppliers page - Manage product-supplier relationships
     */
    public function link()
    {
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 25;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get unlinked products with pagination
        $supplierLinks = $this->productSupplierModel->getUnlinkedProducts($page, $perPage, $search);
        $totalRecords = $this->productSupplierModel->getUnlinkedProductsCount($search);

        // Calculate pagination info
        $totalPages = ceil($totalRecords / $perPage);
        $startRecord = ($page - 1) * $perPage + 1;
        $endRecord = min($page * $perPage, $totalRecords);

        // Get statistics for cards
        $stats = $this->productSupplierModel->getSupplierLinkStats();

        // Get all active products and suppliers for dropdowns
        $products = $this->productModel->getActiveProducts();
        $suppliers = $this->supplierModel->getActiveSuppliers();

        $data = [
            'supplier_links' => $supplierLinks,
            'total_links' => $stats['total_links'] ?? 0,
            'active_links' => $stats['active_links'] ?? 0,
            'linked_products' => $stats['linked_products'] ?? 0,
            'available_suppliers' => $stats['available_suppliers'] ?? 0,
            'unlinked_products' => $stats['unlinked_products'] ?? 0,
            'products' => $products,
            'suppliers' => $suppliers,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_records' => $totalRecords,
                'start_record' => $startRecord,
                'end_record' => $endRecord
            ]
        ];

        parent::view('suppliers/link', $data);
    }

    /**
     * Create a new supplier link (AJAX)
     */
    public function createLink()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate and sanitize input data
            $productId = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT);
            $supplierId = filter_var($_POST['supplier_id'] ?? '', FILTER_VALIDATE_INT);
            $costPrice = filter_var($_POST['supplier_cost_price'] ?? 0, FILTER_VALIDATE_FLOAT);
            $leadTime = filter_var($_POST['lead_time_days'] ?? 7, FILTER_VALIDATE_INT);
            $minOrderQty = filter_var($_POST['min_order_quantity'] ?? 1, FILTER_VALIDATE_INT);
            $linkType = $_POST['link_type'] ?? 'secondary';
            $notes = trim($_POST['notes'] ?? '');

            // Validate required fields
            if (!$productId || !$supplierId) {
                echo json_encode(['success' => false, 'error' => 'Valid Product ID and Supplier ID are required']);
                return;
            }

            // Ensure positive values
            if ($costPrice < 0)
                $costPrice = 0;
            if ($leadTime < 1)
                $leadTime = 7;
            if ($minOrderQty < 1)
                $minOrderQty = 1;

            $data = [
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'purchase_price' => $costPrice,
                'lead_time_days' => $leadTime,
                'min_order_quantity' => $minOrderQty,
                'is_primary' => ($linkType === 'primary') ? 1 : 0,
                'notes' => $notes,
                'is_active' => 1,
                'currency' => 'INR',
                'supplier_sku' => null,
                'supplier_name_for_product' => null,
                'payment_terms' => null,
                'shipping_cost' => 0.00,
                'discount_percentage' => 0.00,
                'quality_rating' => null,
                'delivery_rating' => null
            ];

            try {
                // Check if link already exists
                if ($this->productSupplierModel->linkExists($data['product_id'], $data['supplier_id'])) {
                    echo json_encode(['success' => false, 'error' => 'This product-supplier link already exists']);
                    return;
                }

                $result = $this->productSupplierModel->addProductSupplier($data);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Supplier link created successfully']);
                } else {
                    // Get more detailed error information
                    $db = new Database();
                    $lastError = $db->getLastError();
                    error_log("Failed to create supplier link. Data: " . print_r($data, true));
                    error_log("Database error: " . $lastError);
                    echo json_encode(['success' => false, 'error' => 'Failed to create supplier link - database error: ' . $lastError]);
                }
            } catch (Exception $e) {
                error_log("Create Link Exception: " . $e->getMessage());
                error_log("Exception data: " . print_r($data, true));
                echo json_encode(['success' => false, 'error' => 'Database error occurred: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    /**
     * Update link status (AJAX)
     */
    public function updateLinkStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $linkId = $_POST['link_id'] ?? '';
            $status = $_POST['status'] ?? '';

            if (empty($linkId) || empty($status)) {
                echo json_encode(['success' => false, 'error' => 'Link ID and status are required']);
                return;
            }

            $isActive = $status === 'active' ? 1 : 0;

            if ($this->productSupplierModel->updateLinkStatus($linkId, $isActive)) {
                echo json_encode(['success' => true, 'message' => 'Link status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update link status']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    // Removed setPrimaryLink method - replaced by smart supplier selection

    /**
     * Get existing supplier links for a product (AJAX)
     */
    public function getProductLinks($productId = null)
    {
        header('Content-Type: application/json');

        if (empty($productId)) {
            echo json_encode(['success' => false, 'error' => 'Product ID is required']);
            return;
        }

        try {
            $links = $this->productSupplierModel->getProductSupplierLinks($productId);
            echo json_encode(['success' => true, 'links' => $links]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to fetch product links']);
        }
    }

    /**
     * Bulk update link status (AJAX)
     */
    public function bulkUpdateLinkStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $links = $_POST['links'] ?? '';
            $action = $_POST['action'] ?? '';

            if (empty($links) || empty($action)) {
                echo json_encode(['success' => false, 'error' => 'Links and action are required']);
                return;
            }

            $linkIds = explode(',', $links);
            $isActive = $action === 'activate' ? 1 : 0;

            $success = true;
            foreach ($linkIds as $linkId) {
                if (!$this->productSupplierModel->updateLinkStatus(trim($linkId), $isActive)) {
                    $success = false;
                    break;
                }
            }

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Links updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update some links']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    /**
     * Search suppliers by name or contact information
     * Returns JSON response for AJAX requests
     */
    public function search()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
            echo json_encode([]);
            return;
        }

        $query = trim($_GET['q']);

        try {
            // Search suppliers using a method we'll add to the model
            $suppliers = $this->supplierModel->searchSuppliers($query);
            echo json_encode($suppliers);
        } catch (Exception $e) {
            error_log('Error in supplier search: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
    }

    /**
     * Bulk archive (soft delete) suppliers
     */
    public function bulkDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json');

            try {
                $supplierIds = $_POST['supplier_ids'] ?? [];

                if (empty($supplierIds)) {
                    echo json_encode([
                        'success' => false,
                        'error' => 'No suppliers selected for archiving'
                    ]);
                    exit;
                }

                $successCount = 0;
                $totalCount = count($supplierIds);

                foreach ($supplierIds as $supplierId) {
                    if ($this->supplierModel->deleteSupplier($supplierId)) {
                        $successCount++;
                    }
                }

                if ($successCount == $totalCount) {
                    echo json_encode([
                        'success' => true,
                        'message' => "$successCount suppliers archived successfully. All records and history have been preserved."
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'error' => "Only $successCount out of $totalCount suppliers were archived successfully"
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Error archiving suppliers: ' . $e->getMessage()
                ]);
            }
            exit;
        } else {
            redirect('suppliers');
        }
    }

    /**
     * Generate Supplier Competition Report
     */
    public function competitionReport()
    {
        // Initialize session for competition targets if not exists
        if (!isset($_SESSION['competition_targets'])) {
            $_SESSION['competition_targets'] = [];
        }

        // Get all products with multiple suppliers
        $competitionData = $this->getCompetitionAnalysis();

        $data = [
            'title' => 'Supplier Competition Report',
            'competition_data' => $competitionData,
            'total_potential_savings' => $this->calculateTotalPotentialSavings($competitionData),
            'products_analyzed' => count($competitionData)
        ];

        $this->view('suppliers/competition_report', $data);
    }    /**
         * Get competition analysis data
         */
    private function getCompetitionAnalysis()
    {
        $db = new Database();

        // Get products with multiple suppliers (2 or more)
        $db->query("
            SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                COUNT(ps.supplier_id) as supplier_count,
                MIN(ps.purchase_price) as lowest_price,
                MAX(ps.purchase_price) as highest_price,
                AVG(ps.purchase_price) as average_price
            FROM products p
            INNER JOIN product_suppliers ps ON p.product_id = ps.product_id
            WHERE ps.purchase_price > 0
            GROUP BY p.product_id, p.product_name, p.sku
            HAVING COUNT(ps.supplier_id) >= 2
            ORDER BY (MAX(ps.purchase_price) - MIN(ps.purchase_price)) DESC
        ");

        $db->execute();
        $products = $db->resultSet();

        $competitionData = [];

        foreach ($products as $product) {
            // Get detailed supplier info for this product
            $db->query("
                SELECT 
                    s.supplier_id,
                    s.supplier_name,
                    s.contact_person,
                    s.email,
                    s.phone,
                    s.default_delivery_days,
                    ps.purchase_price,
                    ps.is_primary,
                    ps.lead_time_days,
                    ps.quality_rating as supplier_rating,
                    ps.lead_time_days as delivery_time
                FROM product_suppliers ps
                INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
                WHERE ps.product_id = :product_id
                AND ps.purchase_price > 0
                ORDER BY ps.purchase_price ASC
            ");

            $db->bind(':product_id', $product->product_id);
            $db->execute();
            $suppliers = $db->resultSet();

            if (count($suppliers) >= 2) {
                $lowestPrice = $suppliers[0]->purchase_price;
                $competitionOpportunities = [];

                foreach ($suppliers as $supplier) {
                    // Check for custom target price in session
                    $key = $product->product_id . '_' . $supplier->supplier_id;
                    $customTargetPrice = $_SESSION['competition_targets'][$key] ?? null;

                    // Use custom target price if set, otherwise use lowest price
                    $targetPrice = $customTargetPrice ?: $lowestPrice;

                    if ($supplier->purchase_price > $targetPrice) {
                        $competitionOpportunities[] = [
                            'supplier' => $supplier,
                            'current_price' => $supplier->purchase_price,
                            'target_price' => $targetPrice,
                            'custom_target' => $customTargetPrice ? true : false,
                            'potential_savings' => $supplier->purchase_price - $targetPrice,
                            'savings_percentage' => (($supplier->purchase_price - $targetPrice) / $supplier->purchase_price) * 100
                        ];
                    }
                }

                if (!empty($competitionOpportunities)) {
                    $competitionData[] = [
                        'product' => $product,
                        'suppliers' => $suppliers,
                        'lowest_supplier' => $suppliers[0],
                        'competition_opportunities' => $competitionOpportunities,
                        'total_potential_savings' => array_sum(array_column($competitionOpportunities, 'potential_savings'))
                    ];
                }
            }
        }

        return $competitionData;
    }

    /**
     * Calculate total potential savings across all products
     */
    private function calculateTotalPotentialSavings($competitionData)
    {
        $totalSavings = 0;
        foreach ($competitionData as $product) {
            $totalSavings += $product['total_potential_savings'];
        }
        return $totalSavings;
    }

    /**
     * Export competition report as CSV
     */
    public function exportCompetitionReport()
    {
        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="supplier_competition_report_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV column headers
        fputcsv($output, [
            'Product Name',
            'SKU',
            'Supplier Name',
            'Contact Person',
            'Email',
            'Phone',
            'Current Price',
            'Target Price (Lowest)',
            'Potential Savings',
            'Savings Percentage',
            'Is Primary Supplier',
            'Lead Time Days',
            'Supplier Rating'
        ]);

        // Get competition data
        $competitionData = $this->getCompetitionAnalysis();

        foreach ($competitionData as $productData) {
            foreach ($productData['competition_opportunities'] as $opportunity) {
                fputcsv($output, [
                    $productData['product']->product_name,
                    $productData['product']->sku,
                    $opportunity['supplier']->supplier_name,
                    $opportunity['supplier']->contact_person ?? '',
                    $opportunity['supplier']->email ?? '',
                    $opportunity['supplier']->phone ?? '',
                    number_format($opportunity['current_price'], 2),
                    number_format($opportunity['target_price'], 2),
                    number_format($opportunity['potential_savings'], 2),
                    number_format($opportunity['savings_percentage'], 2) . '%',
                    $opportunity['supplier']->is_primary ? 'Yes' : 'No',
                    $opportunity['supplier']->lead_time_days ?? '',
                    $opportunity['supplier']->supplier_rating ?? ''
                ]);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Update target price for competition analysis
     */
    public function updateTargetPrice()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            $productId = $_POST['product_id'] ?? null;
            $supplierId = $_POST['supplier_id'] ?? null;
            $targetPrice = $_POST['target_price'] ?? null;

            if (!$productId || !$supplierId) {
                throw new Exception('Product ID and Supplier ID are required');
            }

            if (!$targetPrice || $targetPrice <= 0) {
                throw new Exception('Valid target price is required');
            }

            // Store target price in session for this competition analysis
            if (!isset($_SESSION['competition_targets'])) {
                $_SESSION['competition_targets'] = [];
            }

            $key = $productId . '_' . $supplierId;
            $_SESSION['competition_targets'][$key] = floatval($targetPrice);

            echo json_encode([
                'success' => true,
                'message' => 'Target price updated successfully',
                'new_target_price' => number_format($targetPrice, 2),
                'product_id' => $productId,
                'supplier_id' => $supplierId
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update delivery time for competition analysis
     */
    public function updateDeliveryTime()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }

            $productId = $_POST['product_id'] ?? null;
            $supplierId = $_POST['supplier_id'] ?? null;
            $deliveryTime = $_POST['delivery_time'] ?? null;

            if (!$productId || !$supplierId) {
                throw new Exception('Product ID and Supplier ID are required');
            }

            if (!$deliveryTime || $deliveryTime < 1 || $deliveryTime > 365) {
                throw new Exception('Valid delivery time (1-365 days) is required');
            }

            // Update delivery time in database
            $db = new Database();
            $db->query("
                UPDATE product_suppliers 
                SET lead_time_days = :delivery_time 
                WHERE product_id = :product_id 
                AND supplier_id = :supplier_id
            ");

            $db->bind(':delivery_time', intval($deliveryTime));
            $db->bind(':product_id', $productId);
            $db->bind(':supplier_id', $supplierId);

            if ($db->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Delivery time updated successfully',
                    'new_delivery_time' => $deliveryTime,
                    'product_id' => $productId,
                    'supplier_id' => $supplierId
                ]);
            } else {
                throw new Exception('Failed to update delivery time in database');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get products linked to a supplier
    public function getSupplierProducts($supplierId)
    {
        header('Content-Type: application/json');

        try {
            // Get products linked to this supplier
            $products = $this->supplierModel->getLinkedProducts($supplierId);

            echo json_encode([
                'success' => true,
                'products' => $products ?: []
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'products' => []
            ]);
        }
    }

    // Link a product to a supplier
    public function linkProduct()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            // Validate authentication first. Allow AJAX requests (inline edits) from same-origin
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if (!isLoggedIn() && !$isAjax) {
                throw new Exception('Authentication required. Please login first.');
            }

            $productId = $_POST['product_id'] ?? null;
            $supplierId = $_POST['supplier_id'] ?? null;
            $purchasePrice = $_POST['purchase_price'] ?? null;
            // Support update flow when ps_id or link_id is provided
            $psId = $_POST['ps_id'] ?? $_POST['link_id'] ?? null;
            $supplierSku = $_POST['supplier_sku'] ?? '';
            $leadTimeDays = $_POST['lead_time_days'] ?? 7;
            $minOrderQuantity = $_POST['min_order_quantity'] ?? 1;
            $supplierNotes = $_POST['supplier_notes'] ?? '';
            $supplierRating = $_POST['supplier_rating'] ?? 4;

            // Debug: Log received data
            error_log("LinkProduct received: product_id=$productId, supplier_id=$supplierId, price=$purchasePrice, ps_id=$psId");

            // If ps_id is provided, treat this as an update to an existing product_supplier link
            if ($psId) {
                // purchase_price must be provided (can be zero)
                if (!isset($_POST['purchase_price'])) {
                    throw new Exception('Purchase price is required for update');
                }
                if (!is_numeric($purchasePrice) || $purchasePrice < 0) {
                    throw new Exception('Purchase price must be a non-negative number');
                }

                $updateData = [
                    'ps_id' => $psId,
                    'purchase_price' => $purchasePrice,
                    'supplier_sku' => $supplierSku,
                    'lead_time_days' => $leadTimeDays,
                    'min_order_quantity' => $minOrderQuantity,
                    'notes' => $supplierNotes,
                    'supplier_rating' => $supplierRating,
                    'is_active' => 1
                ];

                $result = $this->productSupplierModel->updateProductSupplier($updateData);
                if ($result) {
                    // Return updated price for client-side display
                    echo json_encode([
                        'success' => true,
                        'message' => 'Link updated successfully',
                        'updated_price' => number_format(floatval($purchasePrice), 2),
                        'updated_price_raw' => floatval($purchasePrice)
                    ]);
                } else {
                    throw new Exception('Failed to update product-supplier link');
                }
                return;
            }

            // Creation flow: validate inputs (purchase price must be positive for creation)
            if (empty($productId) || empty($supplierId) || !isset($_POST['purchase_price'])) {
                throw new Exception('Product ID, Supplier ID, and purchase price are required');
            }

            if (!is_numeric($purchasePrice) || $purchasePrice <= 0) {
                throw new Exception('Purchase price must be a positive number');
            }

            // Check if link already exists
            if ($this->supplierModel->isProductLinked($supplierId, $productId)) {
                throw new Exception('This product is already linked to this supplier');
            }

            // Create the link
            $linkData = [
                'supplier_id' => $supplierId,
                'product_id' => $productId,
                'purchase_price' => $purchasePrice,
                'supplier_sku' => $supplierSku,
                'lead_time_days' => $leadTimeDays,
                'min_order_quantity' => $minOrderQuantity,
                'notes' => $supplierNotes,
                'supplier_rating' => $supplierRating,
                'is_active' => 1
            ];

            $result = $this->productSupplierModel->addProductSupplier($linkData);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product linked successfully'
                ]);
            } else {
                // Get more detailed error information
                $db = new Database();
                $lastError = $db->getLastError();
                error_log("Failed to create supplier link. Data: " . print_r($linkData, true));
                error_log("Database error: " . $lastError);
                echo json_encode(['success' => false, 'error' => 'Failed to create supplier link - database error: ' . $lastError]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Unlink a product from a supplier
    public function unlinkProduct()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        try {
            $productId = $_POST['product_id'] ?? null;
            $supplierId = $_POST['supplier_id'] ?? null;

            if (!$productId || !$supplierId) {
                throw new Exception('Product ID and Supplier ID are required');
            }

            $result = $this->supplierModel->unlinkProduct($supplierId, $productId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product unlinked successfully'
                ]);
            } else {
                throw new Exception('Failed to unlink product');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ensure delivery time consistency when creating product-supplier relationships
     * This function should be called after creating new product-supplier links
     */
    private function ensureDeliveryTimeConsistency($productId = null, $supplierId = null)
    {
        $db = new Database();

        $whereClause = "WHERE s.default_delivery_days IS NOT NULL AND s.default_delivery_days > 0";
        $params = [];

        if ($productId && $supplierId) {
            $whereClause .= " AND ps.product_id = :product_id AND ps.supplier_id = :supplier_id";
            $params[':product_id'] = $productId;
            $params[':supplier_id'] = $supplierId;
        }

        $db->query("
            UPDATE product_suppliers ps
            INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
            SET ps.lead_time_days = s.default_delivery_days,
                ps.updated_at = NOW()
            {$whereClause}
            AND (ps.lead_time_days IS NULL OR ps.lead_time_days = 0 OR ps.lead_time_days = 7)
        ");

        foreach ($params as $param => $value) {
            $db->bind($param, $value);
        }

        return $db->execute();
    }
}
