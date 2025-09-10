<?php
/**
 * Customer Management Controller
 * Handles customer directory and management functionality
 */
class CustomerController extends Controller
{
    private $customerModel;
    private $userModel;

    public function __construct()
    {
        $this->customerModel = $this->model('Customer');
        $this->userModel = $this->model('User');
    }

    /**
     * Customer Directory - Public listing with KPIs
     */
    public function index()
    {
        // Get all customers with analytics
        $customers = $this->getAllCustomersWithAnalytics();

        // Calculate KPIs
        $kpis = $this->calculateCustomerKPIs($customers);

        $data = [
            'title' => 'Customer Directory',
            'customers' => $customers,
            'kpis' => $kpis
        ];

        $this->view('customer/index', $data);
    }

    /**
     * Get all customers with analytics data
     */
    private function getAllCustomersWithAnalytics()
    {
        try {
            $db = new Database();
            $db->query("SELECT 
                customer_id,
                customer_name,
                unique_id,
                contact_info,
                credit_limit,
                status,
                discount_credit_balance,
                total_discount_earned,
                total_discount_used,
                deleted_at
                FROM customers 
                WHERE deleted_at IS NULL
                ORDER BY customer_name ASC");

            $db->execute();
            $customers = $db->resultSet();

            // Process contact_info and add calculated fields
            foreach ($customers as $customer) {
                // Parse contact_info (could be JSON or string)
                $contactInfo = $customer->contact_info;
                if (is_string($contactInfo) && strpos($contactInfo, '{') === 0) {
                    // JSON format
                    $contact = json_decode($contactInfo, true);
                    $customer->email = $contact['email'] ?? 'N/A';
                    $customer->phone = $contact['phone'] ?? 'N/A';
                    $customer->contact_person = $contact['contact_person'] ?? $customer->customer_name;
                } else {
                    // String format (phone, email)
                    $parts = explode(',', $contactInfo);
                    $customer->phone = trim($parts[0] ?? 'N/A');
                    $customer->email = trim($parts[1] ?? 'N/A');
                    $customer->contact_person = $customer->customer_name;
                }

                // Convert status to is_active
                $customer->is_active = ($customer->status === 'active') ? 1 : 0;

                // Add missing fields with defaults
                $customer->customer_type = 'business'; // Default type
                $customer->total_orders = 0;
                $customer->total_spent = 0;
                $customer->last_order_date = null;
            }

            // Get order statistics for customers
            $this->addOrderStatistics($customers);

            return $customers;
        } catch (Exception $e) {
            error_log('Error fetching customers: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Add order statistics to customers
     */
    private function addOrderStatistics($customers)
    {
        try {
            $db = new Database();

            foreach ($customers as $customer) {
                // Get order count and total spent from sales table
                $db->query("SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_spent,
                    MAX(sale_date) as last_order_date
                    FROM sales 
                    WHERE customer_id = ?");
                $db->bind(1, $customer->customer_id);
                $db->execute();
                $stats = $db->single();

                if ($stats) {
                    $customer->total_orders = $stats->total_orders;
                    $customer->total_spent = $stats->total_spent;
                    $customer->last_order_date = $stats->last_order_date;
                }
            }
        } catch (Exception $e) {
            error_log('Error adding order statistics: ' . $e->getMessage());
        }
    }    /**
         * Calculate customer KPIs
         */
    private function calculateCustomerKPIs($customers)
    {
        $kpis = [
            'total_customers' => 0,
            'active_customers' => 0,
            'total_revenue' => 0,
            'avg_order_value' => 0,
            'business_customers' => 0,
            'individual_customers' => 0,
            'contractor_customers' => 0,
            'retail_customers' => 0,
            'new_customers_today' => 0,
            'orders_this_week' => 0,
            'revenue_this_month' => 0
        ];

        $totalOrders = 0;
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('-7 days'));
        $monthStart = date('Y-m-01');

        foreach ($customers as $customer) {
            $kpis['total_customers']++;

            if ($customer->is_active == 1) {
                $kpis['active_customers']++;
            }

            $kpis['total_revenue'] += $customer->total_spent ?? 0;
            $totalOrders += $customer->total_orders ?? 0;

            // Customer type distribution
            $customerType = $customer->customer_type ?? 'individual';
            switch ($customerType) {
                case 'business':
                    $kpis['business_customers']++;
                    break;
                case 'contractor':
                    $kpis['contractor_customers']++;
                    break;
                case 'retail':
                    $kpis['retail_customers']++;
                    break;
                default:
                    $kpis['individual_customers']++;
                    break;
            }

            // New customers today
            if (isset($customer->created_at) && date('Y-m-d', strtotime($customer->created_at)) == $today) {
                $kpis['new_customers_today']++;
            }
        }

        // Calculate average order value
        if ($totalOrders > 0) {
            $kpis['avg_order_value'] = $kpis['total_revenue'] / $totalOrders;
        }

        // Get additional analytics from database
        try {
            $db = new Database();

            // Orders this week
            $db->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= ?");
            $db->bind(1, $weekStart);
            $db->execute();
            $result = $db->single();
            $kpis['orders_this_week'] = $result->count ?? 0;

            // Revenue this month
            $db->query("SELECT SUM(total_amount) as revenue FROM orders WHERE created_at >= ?");
            $db->bind(1, $monthStart);
            $db->execute();
            $result = $db->single();
            $kpis['revenue_this_month'] = $result->revenue ?? 0;

        } catch (Exception $e) {
            error_log('Error calculating additional KPIs: ' . $e->getMessage());
        }

        return $kpis;
    }

    /**
     * Toggle customer status (AJAX)
     */
    public function toggleCustomerStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerId = $_POST['customer_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($customerId && in_array($status, ['active', 'inactive'])) {
                try {
                    $db = new Database();
                    $db->query("UPDATE customers SET status = ? WHERE customer_id = ?");
                    $db->bind(1, $status);
                    $db->bind(2, $customerId);
                    $db->execute();

                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        }
    }

    /**
     * View customer details
     */
    public function viewCustomer($customerId)
    {
        try {
            $db = new Database();
            $db->query("SELECT * FROM customers WHERE customer_id = ?");
            $db->bind(1, $customerId);
            $db->execute();
            $customer = $db->single();

            if (!$customer) {
                flash('error', 'Customer not found', 'alert alert-danger');
                redirect('customer');
            }

            // Get customer orders
            $db->query("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10");
            $db->bind(1, $customerId);
            $db->execute();
            $orders = $db->resultSet();

            $data = [
                'title' => 'Customer Details',
                'customer' => $customer,
                'orders' => $orders
            ];

            $this->view('admin/viewCustomer', $data);

        } catch (Exception $e) {
            flash('error', 'Error loading customer details', 'alert alert-danger');
            redirect('customer');
        }
    }

    /**
     * Edit Customer
     */
    public function edit($customerId = null)
    {
        if (!$customerId) {
            flash('customer_message', 'Customer ID is required', 'alert alert-danger');
            redirect('customer');
        }

        // Get customer data
        $customer = $this->customerModel->getCustomerById($customerId);

        if (!$customer) {
            flash('customer_message', 'Customer not found', 'alert alert-danger');
            redirect('customer');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            ob_clean();
            header('Content-Type: application/json');

            try {
                $_POST = sanitizePost($_POST);

                // Customer data
                $customerData = [
                    'id' => $customerId,
                    'customer_name' => trim($_POST['customer_name'] ?? ''),
                    'contact_info' => trim($_POST['contact_info'] ?? ''),
                    'credit_limit' => floatval($_POST['credit_limit'] ?? 0),
                    'customer_type' => $_POST['customer_type'] ?? 'business',
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Validate data
                $errors = [];
                if (empty($customerData['customer_name'])) {
                    $errors[] = 'Customer name is required';
                }
                if ($customerData['credit_limit'] < 0) {
                    $errors[] = 'Credit limit cannot be negative';
                }

                if (empty($errors)) {
                    if ($this->customerModel->updateCustomer($customerData)) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Customer updated successfully',
                            'redirect' => 'admin/viewCustomer/' . $customerId
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update customer']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                }
            } catch (Exception $e) {
                error_log('Edit customer error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server error occurred']);
            }
            exit();
        }

        // GET request - show form
        $data = [
            'title' => 'Edit Customer - ' . $customer->customer_name,
            'customer' => $customer
        ];
        $this->view('customer/edit', $data);
    }
}
?>