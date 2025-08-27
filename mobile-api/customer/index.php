<?php
/**
 * Customer Mobile API
 * Handles customer-specific mobile app requests
 */

require_once '../../bootstrap.php';
require_once '../auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

class CustomerMobileAPI
{
    private $customerModel;
    private $auth;

    public function __construct()
    {
        $this->customerModel = new Customer();
        $this->auth = new MobileAuthAPI();
    }

    /**
     * Authenticate request
     */
    private function authenticate()
    {
        $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            return false;
        }

        $user = $this->auth->getAuthenticatedUser($token);

        if (!$user || $user['user_type'] !== 'customer') {
            return false;
        }

        return $user;
    }

    /**
     * Get customer dashboard data
     */
    public function getDashboard()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Get customer details
        $customer = $this->customerModel->getCustomerById($user['user_id']);

        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found'];
        }

        // Get customer stats (placeholder - implement based on your order system)
        $stats = [
            'active_orders'    => 0,
            'completed_orders' => 0,
            'pending_orders'   => 0,
            'total_spent'      => 0.00
        ];

        return [
            'success' => true,
            'data'    => [
                'customer' => [
                    'id'     => $customer->customer_id,
                    'name'   => $customer->name,
                    'email'  => $customer->email,
                    'phone'  => $customer->phone,
                    'status' => $customer->status
                ],
                'stats'    => $stats
            ]
        ];
    }

    /**
     * Get customer profile
     */
    public function getProfile()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $customer = $this->customerModel->getCustomerById($user['user_id']);

        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found'];
        }

        // Parse contact info if it's JSON
        $contactInfo = [];
        if (!empty($customer->contact_info)) {
            $contactInfo = json_decode($customer->contact_info, true) ?: [];
        }

        return [
            'success' => true,
            'data'    => [
                'id'           => $customer->customer_id,
                'name'         => $customer->name,
                'email'        => $customer->email,
                'phone'        => $customer->phone,
                'address'      => $customer->address ?? '',
                'contact_info' => $contactInfo,
                'status'       => $customer->status,
                'created_at'   => $customer->created_at ?? ''
            ]
        ];
    }

    /**
     * Update customer profile
     */
    public function updateProfile($data)
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Validate input
        $updateData = [
            'name'    => trim($data['name'] ?? ''),
            'phone'   => trim($data['phone'] ?? ''),
            'address' => trim($data['address'] ?? ''),
        ];

        if (empty($updateData['name'])) {
            return ['success' => false, 'message' => 'Name is required'];
        }

        // Handle contact info
        $contactInfo = [];
        if (isset($data['contact_info']) && is_array($data['contact_info'])) {
            $contactInfo = $data['contact_info'];
        }

        // Update profile
        $result = $this->customerModel->updateCustomerProfile($user['user_id'], $updateData, $contactInfo);

        if ($result) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    /**
     * Get customer orders (placeholder - implement based on your order system)
     */
    public function getOrders()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Placeholder - implement based on your order/purchase system
        return [
            'success' => true,
            'data'    => [
                'orders'  => [],
                'message' => 'Order system integration pending'
            ]
        ];
    }
}

// Handle requests
$api = new CustomerMobileAPI();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($method) {
        case 'GET':
            if (strpos($path, '/dashboard') !== false) {
                $result = $api->getDashboard();
            } elseif (strpos($path, '/profile') !== false) {
                $result = $api->getProfile();
            } elseif (strpos($path, '/orders') !== false) {
                $result = $api->getOrders();
            } else {
                $result = ['success' => false, 'message' => 'Endpoint not found'];
            }
            echo json_encode($result);
            break;

        case 'PUT':
            if (strpos($path, '/profile') !== false) {
                $result = $api->updateProfile($request);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>