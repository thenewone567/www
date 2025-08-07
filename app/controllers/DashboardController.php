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
        // Sales Performance Data
        $totalSales = $this->dashboardModel->getTotalSales(30);
        $salesGrowth = $this->dashboardModel->getSalesGrowth(30);
        $avgTransaction = $this->dashboardModel->getAverageTransactionValue(30);
        $totalTransactions = $this->dashboardModel->getTotalTransactions(30);
        $topSelling = $this->dashboardModel->getTopSellingProducts(5, 30);
        $salesByCategory = $this->dashboardModel->getSalesByCategory(30);

        // Inventory Management Data
        $inventoryValue = $this->dashboardModel->getTotalInventoryValue();
        $totalProducts = $this->dashboardModel->getTotalProducts();
        $lowInventory = $this->dashboardModel->getLowInventoryProducts(10);
        $lowInventoryCount = $this->dashboardModel->getLowInventoryCount();
        $outOfInventoryCount = $this->dashboardModel->getOutOfInventoryCount();
        $outOfInventoryPercentage = $this->dashboardModel->getOutOfInventoryPercentage();

        // Customer Analytics Data
        $newCustomers = $this->dashboardModel->getNewCustomers(30);

        // Financial Data
        $grossMargin = $this->dashboardModel->getGrossMargin(30);

        // Legacy data for backward compatibility
        $salesToday = $this->dashboardModel->getSalesToday();
        $salesWeek = $this->dashboardModel->getSalesWeek();
        $salesMonth = $this->dashboardModel->getSalesMonth();

        // Prepare comprehensive data array
        $data = [
            'title' => 'Hardware Store Dashboard',

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
            'low_Inventory' => is_array($lowInventory) ? $lowInventory : [],
            'low_Inventory_count' => $lowInventoryCount,
            'out_of_Inventory_count' => $outOfInventoryCount,
            'out_of_Inventory_percentage' => $outOfInventoryPercentage,

            // Customer Analytics
            'new_customers' => $newCustomers,

            // Financial Metrics
            'gross_margin' => $grossMargin,

            // Legacy data (for backward compatibility)
            'sales_today' => $salesToday !== null ? $salesToday : 0,
            'sales_week' => $salesWeek !== null ? $salesWeek : 0,
            'sales_month' => $salesMonth !== null ? $salesMonth : 0,
        ];

        $this->view('dashboard/index', $data);
    }

    // AJAX endpoint for real-time data updates
    public function getData()
    {
        header('Content-Type: application/json');

        $days = $_GET['days'] ?? 30;

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

        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }
}
