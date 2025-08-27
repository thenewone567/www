<?php
/**
 * Mobile API Authentication Handler
 * Handles JWT tokens and mobile app authentication
 */

require_once '../bootstrap.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

class MobileAuthAPI
{
    private $userModel;
    private $customerModel;
    private $contractorModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->customerModel = new Customer();
        $this->contractorModel = new Contractor();
    }

    /**
     * Generate JWT token
     */
    private function generateJWT($payload)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($payload);

        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, JWT_SECRET, true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    /**
     * Validate JWT token
     */
    public function validateJWT($token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, JWT_SECRET, true);
        $expectedSignature = $this->base64UrlEncode($signature);

        if ($signatureEncoded !== $expectedSignature) {
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Customer Login
     */
    public function customerLogin($email, $password)
    {
        $customer = $this->customerModel->getCustomerByEmail($email);

        if (!$customer) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!password_verify($password, $customer->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if ($customer->status !== 'active') {
            return ['success' => false, 'message' => 'Account is not active'];
        }

        $payload = [
            'user_id'   => $customer->customer_id,
            'user_type' => 'customer',
            'email'     => $customer->email,
            'name'      => $customer->name,
            'exp'       => time() + (7 * 24 * 60 * 60) // 7 days
        ];

        $token = $this->generateJWT($payload);

        return [
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'    => $customer->customer_id,
                'name'  => $customer->name,
                'email' => $customer->email,
                'type'  => 'customer'
            ]
        ];
    }

    /**
     * Contractor Login
     */
    public function contractorLogin($email, $password)
    {
        $contractor = $this->contractorModel->getContractorByEmail($email);

        if (!$contractor) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!password_verify($password, $contractor->password)) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!$contractor->status) {
            return ['success' => false, 'message' => 'Account is not active'];
        }

        $payload = [
            'user_id'   => $contractor->contractor_id,
            'user_type' => 'contractor',
            'email'     => $contractor->email,
            'name'      => $contractor->name,
            'exp'       => time() + (7 * 24 * 60 * 60) // 7 days
        ];

        $token = $this->generateJWT($payload);

        return [
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'    => $contractor->contractor_id,
                'name'  => $contractor->name,
                'email' => $contractor->email,
                'type'  => 'contractor'
            ]
        ];
    }

    /**
     * Get authenticated user from token
     */
    public function getAuthenticatedUser($token)
    {
        $payload = $this->validateJWT($token);

        if (!$payload) {
            return false;
        }

        return $payload;
    }
}

// Handle requests
$auth = new MobileAuthAPI();
$method = $_SERVER['REQUEST_METHOD'];
$request = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'POST':
            if (isset($request['action'])) {
                switch ($request['action']) {
                    case 'customer_login':
                        $result = $auth->customerLogin($request['email'], $request['password']);
                        echo json_encode($result);
                        break;

                    case 'contractor_login':
                        $result = $auth->contractorLogin($request['email'], $request['password']);
                        echo json_encode($result);
                        break;

                    case 'validate_token':
                        $token = $request['token'] ?? '';
                        $user = $auth->getAuthenticatedUser($token);
                        if ($user) {
                            echo json_encode(['success' => true, 'user' => $user]);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Invalid token']);
                        }
                        break;

                    default:
                        echo json_encode(['success' => false, 'message' => 'Invalid action']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Action required']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>