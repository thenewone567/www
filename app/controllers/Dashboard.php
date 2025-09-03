<?php
class Dashboard extends Controller
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = $this->model('DashboardModel');
    }

    public function index()
    {
        // Debug: Log what we're receiving
        error_log("Dashboard::index() called");
        error_log("GET parameters: " . print_r($_GET, true));
        error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set'));

        // Get period from request (default to 30 days)
        $period = isset($_GET['period']) ? (int) $_GET['period'] : 30;

        // Debug: Log the period being used
        error_log("Period determined: " . $period);

        // Validate period values
        if (!in_array($period, [7, 30, 90])) {
            error_log("Invalid period {$period}, defaulting to 30");
            $period = 30;
        }

        try {
            $data = [
                'period' => $period,
                'total_sales' => $this->dashboardModel->getTotalSales($period),
                'sales_growth' => $this->dashboardModel->getSalesGrowth($period),
                'avg_transaction' => $this->dashboardModel->getAverageTransactionValue($period),
                'total_transactions' => $this->dashboardModel->getTotalTransactions($period),
                'inventory_value' => $this->dashboardModel->getTotalInventoryValue(),
                'total_products' => $this->dashboardModel->getTotalProducts(),
                'low_inventory_count' => $this->dashboardModel->getLowInventoryCount(),
                'out_of_inventory_count' => $this->dashboardModel->getOutOfInventoryCount(),
                'gross_margin' => $this->dashboardModel->getGrossMargin($period),
                'new_customers' => $this->dashboardModel->getNewCustomers($period),
                'recent_sales' => [], // No getRecentSales method available
                'top_products' => $this->dashboardModel->getTopSellingProducts(5, $period)
            ];

            $this->view('dashboard/index', $data);

        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            $data = [
                'error' => 'Failed to load dashboard data',
                'period' => $period
            ];
            $this->view('dashboard/index', $data);
        }
    }

    public function getData()
    {
        header('Content-Type: application/json');

        // Check if this is an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        // Get period from request
        $period = isset($_GET['days']) ? (int) $_GET['days'] : 30;

        // Validate period values
        if (!in_array($period, [7, 30, 90])) {
            echo json_encode(['success' => false, 'message' => 'Invalid period']);
            return;
        }

        try {
            $data = [
                'total_sales' => $this->dashboardModel->getTotalSales($period),
                'sales_growth' => $this->dashboardModel->getSalesGrowth($period),
                'avg_transaction' => $this->dashboardModel->getAverageTransactionValue($period),
                'total_transactions' => $this->dashboardModel->getTotalTransactions($period),
                'inventory_value' => $this->dashboardModel->getTotalInventoryValue(),
                'total_products' => $this->dashboardModel->getTotalProducts(),
                'low_inventory_count' => $this->dashboardModel->getLowInventoryCount(),
                'out_of_inventory_count' => $this->dashboardModel->getOutOfInventoryCount(),
                'gross_margin' => $this->dashboardModel->getGrossMargin($period),
                'new_customers' => $this->dashboardModel->getNewCustomers($period)
            ];

            echo json_encode(['success' => true, 'data' => $data]);

        } catch (Exception $e) {
            error_log("Dashboard getData error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to fetch dashboard data']);
        }
    }
}
?>