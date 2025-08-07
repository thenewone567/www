<?php
/**
 * Transaction Verification API Controller
 * Provides endpoints for real-time transaction verification
 */

class VerificationApiController extends Controller
{
    private $verifier;

    public function __construct()
    {
        // Allow API access without login for verification checks
        $this->verifier = new TransactionVerifier();
    }

    /**
     * Main verification endpoint
     */
    public function verify()
    {
        // Only allow AJAX requests
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Invalid request method'], 400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['transaction_type']) || !isset($input['insert_id'])) {
            $this->jsonResponse(['error' => 'Missing required parameters'], 400);
            return;
        }

        $result = $this->verifier->quickVerify(
            $input['transaction_type'],
            $input['insert_id']
        );

        $this->jsonResponse($result);
    }

    /**
     * Get verification history
     */
    public function history()
    {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
            return;
        }

        $transactionType = $_GET['type'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);

        $history = $this->verifier->getVerificationHistory($transactionType, $limit);

        $this->jsonResponse([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Bulk verification for multiple transactions
     */
    public function bulk()
    {
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['error' => 'Invalid request method'], 400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['transactions']) || !is_array($input['transactions'])) {
            $this->jsonResponse(['error' => 'Invalid transactions data'], 400);
            return;
        }

        $results = [];
        foreach ($input['transactions'] as $transaction) {
            if (isset($transaction['type']) && isset($transaction['id'])) {
                $results[] = [
                    'id' => $transaction['id'],
                    'type' => $transaction['type'],
                    'verification' => $this->verifier->quickVerify($transaction['type'], $transaction['id'])
                ];
            }
        }

        $this->jsonResponse([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * System status check
     */
    public function status()
    {
        try {
            // Test database connection
            $db = new Database();
            $db->query("SELECT 1");
            $db->execute();

            $this->jsonResponse([
                'success' => true,
                'status' => 'operational',
                'database' => 'connected',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'status' => 'error',
                'database' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }

    /**
     * Check if request is AJAX
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>