<?php
/**
 * Customer Dashboard Controller
 * Handles customer portal functionality
 */
class CustomerController extends Controller
{
    private $customerModel;
    private $orderModel;

    public function __construct()
    {
        // Check if user is logged in as customer
        $this->checkCustomerAuth();

        $this->customerModel = $this->model('Customer');
        // Load order model if exists, otherwise skip
        if (class_exists('Order')) {
            $this->orderModel = $this->model('Order');
        }
    }

    private function checkCustomerAuth()
    {
        if (!isset($_SESSION['customer_id'])) {
            redirect('customer/login');
        }
    }

    /**
     * Customer Dashboard Home
     */
    public function index()
    {
        $customerId = $_SESSION['customer_id'];

        // Get customer info
        $customer = $this->customerModel->getCustomerById($customerId);

        // Get recent orders (if order system exists)
        $recentOrders = [];
        if ($this->orderModel) {
            $recentOrders = $this->orderModel->getCustomerRecentOrders($customerId, 5);
        }

        $data = [
            'title'         => 'Customer Dashboard',
            'customer'      => $customer,
            'recent_orders' => $recentOrders
        ];

        $this->view('customer/dashboard', $data);
    }

    /**
     * Customer Profile Management
     */
    public function profile()
    {
        $customerId = $_SESSION['customer_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $updateData = [
                'customer_id'    => $customerId,
                'contact_person' => trim($_POST['contact_person']),
                'email'          => trim($_POST['email']),
                'phone'          => trim($_POST['phone']),
                'address'        => trim($_POST['address']),
                'city'           => trim($_POST['city']),
                'state'          => trim($_POST['state']),
                'zip_code'       => trim($_POST['zip_code'])
            ];

            if ($this->customerModel->updateCustomerProfile($updateData)) {
                flash('profile_success', 'Profile updated successfully', 'alert alert-success');
            } else {
                flash('profile_error', 'Failed to update profile', 'alert alert-danger');
            }
            redirect('customer/profile');
        }

        // GET request
        $customer = $this->customerModel->getCustomerById($customerId);

        $data = [
            'title'    => 'My Profile',
            'customer' => $customer
        ];

        $this->view('customer/profile', $data);
    }

    /**
     * Customer Orders
     */
    public function orders()
    {
        $customerId = $_SESSION['customer_id'];

        $orders = [];
        if ($this->orderModel) {
            $orders = $this->orderModel->getCustomerOrders($customerId);
        }

        $data = [
            'title'  => 'My Orders',
            'orders' => $orders
        ];

        $this->view('customer/orders', $data);
    }

    /**
     * Customer Login
     */
    public function login()
    {
        if (isset($_SESSION['customer_id'])) {
            redirect('customer');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);

            $email = trim($_POST['email']);
            $password = trim($_POST['password']); // For now, we'll implement a simple login

            // Simple authentication - in real app, use proper password hashing
            $customer = $this->customerModel->getCustomerByEmail($email);

            if ($customer && $customer->status === 'active') {
                $_SESSION['customer_id'] = $customer->customer_id;
                $_SESSION['customer_name'] = $customer->customer_name;
                $_SESSION['customer_email'] = $customer->email ?? '';

                flash('login_success', 'Welcome back, ' . $customer->customer_name, 'alert alert-success');
                redirect('customer');
            } else {
                flash('login_error', 'Invalid credentials or account deactivated', 'alert alert-danger');
            }
        }

        $data = [
            'title' => 'Customer Login'
        ];

        $this->view('customer/login', $data);
    }

    /**
     * Customer Logout
     */
    public function logout()
    {
        unset($_SESSION['customer_id']);
        unset($_SESSION['customer_name']);
        unset($_SESSION['customer_email']);

        flash('logout_info', 'You have been logged out', 'alert alert-info');
        redirect('customer/login');
    }
}
?>