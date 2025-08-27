<?php
/**
 * Contractor Mobile API
 * Handles contractor-specific mobile app requests
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

class ContractorMobileAPI
{
    private $contractorModel;
    private $auth;

    public function __construct()
    {
        $this->contractorModel = new Contractor();
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

        if (!$user || $user['user_type'] !== 'contractor') {
            return false;
        }

        return $user;
    }

    /**
     * Get contractor dashboard data
     */
    public function getDashboard()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Get contractor details
        $contractor = $this->contractorModel->getContractorById($user['user_id']);

        if (!$contractor) {
            return ['success' => false, 'message' => 'Contractor not found'];
        }

        // Get contractor stats (placeholder - implement based on your job system)
        $stats = [
            'active_jobs'         => 0,
            'completed_jobs'      => 0,
            'earnings_this_month' => 0.00,
            'total_earnings'      => 0.00
        ];

        return [
            'success' => true,
            'data'    => [
                'contractor' => [
                    'id'          => $contractor->contractor_id,
                    'name'        => $contractor->name,
                    'email'       => $contractor->email,
                    'phone'       => $contractor->phone,
                    'status'      => $contractor->status ? 'active' : 'inactive',
                    'skills'      => $contractor->skills ?? '',
                    'hourly_rate' => $contractor->hourly_rate ?? 0
                ],
                'stats'      => $stats
            ]
        ];
    }

    /**
     * Get contractor profile
     */
    public function getProfile()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $contractor = $this->contractorModel->getContractorById($user['user_id']);

        if (!$contractor) {
            return ['success' => false, 'message' => 'Contractor not found'];
        }

        return [
            'success' => true,
            'data'    => [
                'id'             => $contractor->contractor_id,
                'name'           => $contractor->name,
                'email'          => $contractor->email,
                'phone'          => $contractor->phone,
                'address'        => $contractor->address ?? '',
                'skills'         => $contractor->skills ?? '',
                'experience'     => $contractor->experience ?? '',
                'hourly_rate'    => $contractor->hourly_rate ?? 0,
                'availability'   => $contractor->availability ?? '',
                'certifications' => $contractor->certifications ?? '',
                'status'         => $contractor->status ? 'active' : 'inactive',
                'created_at'     => $contractor->created_at ?? ''
            ]
        ];
    }

    /**
     * Update contractor profile
     */
    public function updateProfile($data)
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Validate input
        $updateData = [
            'name'           => trim($data['name'] ?? ''),
            'phone'          => trim($data['phone'] ?? ''),
            'address'        => trim($data['address'] ?? ''),
            'skills'         => trim($data['skills'] ?? ''),
            'experience'     => trim($data['experience'] ?? ''),
            'hourly_rate'    => floatval($data['hourly_rate'] ?? 0),
            'availability'   => trim($data['availability'] ?? ''),
            'certifications' => trim($data['certifications'] ?? '')
        ];

        if (empty($updateData['name'])) {
            return ['success' => false, 'message' => 'Name is required'];
        }

        // Update profile
        $result = $this->contractorModel->updateContractorProfile($user['user_id'], $updateData);

        if ($result) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    /**
     * Get contractor jobs (placeholder - implement based on your job system)
     */
    public function getJobs()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Placeholder - implement based on your job management system
        return [
            'success' => true,
            'data'    => [
                'jobs'    => [],
                'message' => 'Job management system integration pending'
            ]
        ];
    }

    /**
     * Get contractor earnings (placeholder - implement based on your payment system)
     */
    public function getEarnings()
    {
        $user = $this->authenticate();
        if (!$user) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        // Placeholder - implement based on your payment/earnings system
        return [
            'success' => true,
            'data'    => [
                'earnings'        => [
                    'this_month' => 0.00,
                    'last_month' => 0.00,
                    'total'      => 0.00,
                    'pending'    => 0.00
                ],
                'recent_payments' => [],
                'message'         => 'Earnings system integration pending'
            ]
        ];
    }
}

// Handle requests
$api = new ContractorMobileAPI();
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
            } elseif (strpos($path, '/jobs') !== false) {
                $result = $api->getJobs();
            } elseif (strpos($path, '/earnings') !== false) {
                $result = $api->getEarnings();
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