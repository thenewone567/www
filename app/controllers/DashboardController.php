<?php
class DashboardController extends Controller
{
    public $dashboardModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->dashboardModel = $this->model('Dashboard');
    }

    public function index()
    {
        // Get period from request (default to 30 days)
        $period = isset($_GET['period']) ? (int) $_GET['period'] : 30;

        // Validate period values
        if (!in_array($period, [7, 30, 90])) {
            $period = 30;
        }

        // Implement a short-lived file cache to avoid repeating expensive dashboard queries
        $cacheTtl = 30; // seconds
        $cacheDir = __DIR__ . '/../../cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/dashboard_' . $period . '.json';

        $cachedData = null;
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
            $cached = @file_get_contents($cacheFile);
            $cachedData = $cached ? @json_decode($cached, true) : null;
        }

        if ($cachedData && is_array($cachedData)) {
            // Use cached values to speed up page load
            $totalSales = $cachedData['total_sales'] ?? 0;
            $salesGrowth = $cachedData['sales_growth'] ?? 0;
            $avgTransaction = $cachedData['avg_transaction'] ?? 0;
            $totalTransactions = $cachedData['total_transactions'] ?? 0;
            $topSelling = $cachedData['top_selling'] ?? [];
            $salesByCategory = $cachedData['sales_by_category'] ?? [];
            $dailyTrend = $cachedData['daily_trend'] ?? [];
        } else {
            // Sales Performance Data
            $totalSales = $this->dashboardModel->getTotalSales($period);
            $salesGrowth = $this->dashboardModel->getSalesGrowth($period);
            $avgTransaction = $this->dashboardModel->getAverageTransactionValue($period);
            $totalTransactions = $this->dashboardModel->getTotalTransactions($period);
            $topSelling = $this->dashboardModel->getTopSellingProducts(5, $period);
            $salesByCategory = $this->dashboardModel->getSalesByCategory($period);

            // Daily sales trend used for the Monthly Sales Trend chart (labels + data)
            $dailyTrend = $this->dashboardModel->getDailySalesTrend($period);
        }
        $monthlyLabels = [];
        $monthlySales = [];
        if (is_array($dailyTrend) && count($dailyTrend) > 0) {
            foreach ($dailyTrend as $row) {
                // each row expected to have sale_date and daily_sales
                $monthlyLabels[] = isset($row->sale_date) ? $row->sale_date : (isset($row['sale_date']) ? $row['sale_date'] : '');
                $monthlySales[] = isset($row->daily_sales) ? (float) $row->daily_sales : (isset($row['daily_sales']) ? (float) $row['daily_sales'] : 0);
            }
        }

        // Inventory Management Data
        $inventoryValue = $this->dashboardModel->getTotalInventoryValue();
        $totalProducts = $this->dashboardModel->getTotalProducts();
        $lowInventory = $this->dashboardModel->getLowInventoryProducts(10);
        $lowInventoryCount = $this->dashboardModel->getLowInventoryCount();
        $outOfInventoryCount = $this->dashboardModel->getOutOfInventoryCount();
        $outOfInventoryPercentage = $this->dashboardModel->getOutOfInventoryPercentage();
        // Chart distributions
        $inventoryStatusDistribution = $this->dashboardModel->getInventoryStatusDistribution();
        $priceRangeDistribution = $this->dashboardModel->getPriceRangeDistribution();

        // Customer Analytics Data
        $newCustomers = $this->dashboardModel->getNewCustomers($period);

        // Product Activities Data
        $productActivities = $this->getRecentProductActivities(10);

        // Financial Data
        $grossMargin = $this->dashboardModel->getGrossMargin($period);

        // Legacy data for backward compatibility
        $salesToday = $this->dashboardModel->getSalesToday();
        $salesWeek = $this->dashboardModel->getSalesWeek();
        $salesMonth = $this->dashboardModel->getSalesMonth();

        // Prepare comprehensive data array
        $data = [
            'title' => 'Hardware Store Dashboard',
            'period' => $period, // Add current period for view access

            // Sales Performance
            'total_sales' => $totalSales,
            'sales_growth' => $salesGrowth,
            'avg_transaction' => $avgTransaction,
            'total_transactions' => $totalTransactions,
            'top_selling' => is_array($topSelling) ? $topSelling : [],
            'sales_by_category' => is_array($salesByCategory) ? $salesByCategory : [],

            // Inventory Management
            'inventory_value' => $inventoryValue,
            'total_products' => $totalProducts,
            'low_inventory' => is_array($lowInventory) ? $lowInventory : [],
            'low_inventory_count' => $lowInventoryCount,
            'out_of_inventory_count' => $outOfInventoryCount,
            'out_of_inventory_percentage' => $outOfInventoryPercentage,
            'inventory_status_distribution' => $inventoryStatusDistribution,
            'price_range_distribution' => $priceRangeDistribution,

            // Customer Analytics
            'new_customers' => $newCustomers,

            // Product Activities
            'product_activities' => is_array($productActivities) ? $productActivities : [],

            // Financial Metrics
            'gross_margin' => $grossMargin,

            // Legacy data (for backward compatibility)
            'sales_today' => $salesToday !== null ? $salesToday : 0,
            'sales_week' => $salesWeek !== null ? $salesWeek : 0,
            'sales_month' => $salesMonth !== null ? $salesMonth : 0,
            // Monthly chart data (labels + values) used by the Monthly Sales Trend chart
            'monthly_labels' => $monthlyLabels,
            'monthly_sales' => $monthlySales,
        ];

        // Persist cache (non-blocking)
        if (!($cachedData && is_array($cachedData))) {
            try {
                @file_put_contents($cacheFile, json_encode([
                    'total_sales' => $totalSales,
                    'sales_growth' => $salesGrowth,
                    'avg_transaction' => $avgTransaction,
                    'total_transactions' => $totalTransactions,
                    'top_selling' => $topSelling,
                    'sales_by_category' => $salesByCategory,
                    'daily_trend' => $dailyTrend
                ]));
            } catch (Exception $e) {
                // fail silently to avoid breaking dashboard
            }
        }

        $this->view('dashboard/index', $data);
    }

    // AJAX endpoint for real-time data updates
    public function getData()
    {
        header('Content-Type: application/json');

        $days = $_GET['days'] ?? 30;

        // Short cache for AJAX requests as well
        $cacheTtl = 15; // seconds for AJAX
        $cacheDir = __DIR__ . '/../../cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/dashboard_ajax_' . $days . '.json';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
            $cached = @file_get_contents($cacheFile);
            if ($cached) {
                echo $cached;
                exit;
            }
        }

        $data = [
            'sales_trend' => $this->dashboardModel->getDailySalesTrend($days),
            'total_sales' => $this->dashboardModel->getTotalSales($days),
            'sales_growth' => $this->dashboardModel->getSalesGrowth($days),
            'avg_transaction' => $this->dashboardModel->getAverageTransactionValue($days),
            'top_selling' => $this->dashboardModel->getTopSellingProducts(5, $days),
            'sales_by_category' => $this->dashboardModel->getSalesByCategory($days),
            'low_Inventory' => $this->dashboardModel->getLowInventoryProducts(10),
            'gross_margin' => $this->dashboardModel->getGrossMargin($days),
            'new_customers' => $this->dashboardModel->getNewCustomers($days),
            'out_of_Inventory_percentage' => $this->dashboardModel->getOutOfInventoryPercentage()
        ];

        $payload = json_encode(['success' => true, 'data' => $data]);

        // Save cache (best-effort)
        try {
            @file_put_contents($cacheFile, $payload);
        } catch (Exception $e) {
        }

        echo $payload;
        exit;
    }

    /**
     * Get recent product activities for dashboard
     */
    private function getRecentProductActivities($limit = 10)
    {
        try {
            $db = new Database();

            // Query to get recent product activities from activity_logs table
            $db->query("
                SELECT 
                    al.log_id as id,
                    al.action,
                    COALESCE(p.product_name, 'Unknown Product') as product_name,
                    COALESCE(u.username, 'System') as user_name,
                    CONCAT(al.action, ' - ', COALESCE(p.product_name, CONCAT('Product ID: ', CAST(al.target_id AS CHAR)))) as details,
                    al.log_timestamp as created_at,
                    CASE 
                        WHEN al.action LIKE '%add%' OR al.action LIKE '%create%' THEN 'success'
                        WHEN al.action LIKE '%delete%' OR al.action LIKE '%remove%' THEN 'warning'
                        WHEN al.action LIKE '%error%' OR al.action LIKE '%fail%' THEN 'error'
                        ELSE 'success'
                    END as status
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.user_id
                LEFT JOIN products p ON al.target_type = 'product' AND al.target_id = p.product_id
                WHERE al.target_type = 'product' OR al.action LIKE '%product%'
                ORDER BY al.log_timestamp DESC
                LIMIT :limit
            ");

            $db->bind(':limit', $limit);
            $results = $db->resultSet();

            return $results ? $results : [];

        } catch (Exception $e) {
            error_log('Error fetching recent product activities for dashboard: ' . $e->getMessage());
            return [];
        }
    }
}
