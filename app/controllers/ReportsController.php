<?php
class ReportsController extends Controller
{
    public $reportModel;
    public $inventoryModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->reportModel = $this->model('Report');
        $this->inventoryModel = $this->model('Inventory');
    }

    public function index()
    {
        $this->view('reports/index');
    }

    /**
     * Analytics Dashboard
     */
    public function analytics()
    {
        $period = $_GET['period'] ?? 30;

        // Get analytics data
        $analytics = $this->reportModel->getSalesAnalytics($period);
        $topProducts = $this->reportModel->getTopSellingProducts(5);
        $topCustomers = $this->reportModel->getCustomerAnalysis($period);
        $supplierPerformance = $this->reportModel->getSupplierPerformance();
        $lowInventoryItems = $this->inventoryModel->getLowInventoryItems();

        // Prepare chart data
        $salesTrend = $this->prepareSalesTrendData($period);
        $topProductsChart = $this->prepareTopProductsChart($topProducts);

        $data = [
            'analytics' => $analytics,
            'topProducts' => $topProductsChart,
            'topCustomers' => $topCustomers,
            'supplierPerformance' => $supplierPerformance,
            'lowInventoryItems' => $lowInventoryItems,
            'salesTrend' => $salesTrend
        ];

        $this->view('reports/analytics', $data);
    }

    /**
     * Prepare sales trend data for chart
     */
    private function prepareSalesTrendData($period)
    {
        $salesData = $this->reportModel->getSalesTrendData($period);

        $labels = [];
        $data = [];

        foreach ($salesData as $item) {
            $labels[] = date('M j', strtotime($item->sale_date));
            $data[] = (float) $item->daily_sales;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Prepare top products data for chart
     */
    private function prepareTopProductsChart($products)
    {
        $labels = [];
        $data = [];

        foreach ($products as $product) {
            $labels[] = $product->product_name;
            $data[] = (int) $product->total_quantity;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Generate sales report
     */
    public function sales()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $salesData = $this->reportModel->getSalesReport($startDate, $endDate);
        $summary = $this->reportModel->getSalesSummary($startDate, $endDate);

        $data = [
            'sales' => $salesData,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $this->view('reports/sales', $data);
    }

    /**
     * Generate inventory report
     */
    public function inventory()
    {
        $inventoryData = $this->reportModel->getInventoryReport();
        $lowInventoryItems = $this->inventoryModel->getLowInventoryItems();
        $turnoverData = $this->reportModel->getInventoryTurnover();

        $data = [
            'inventory' => $inventoryData,
            'lowInventory' => $lowInventoryItems,
            'turnover' => $turnoverData
        ];

        $this->view('reports/inventory', $data);
    }

    /**
     * Generate profit analysis report
     */
    public function profit()
    {
        $period = $_GET['period'] ?? 30;
        $profitData = $this->reportModel->getProfitMarginAnalysis($period);

        $data = [
            'profit' => $profitData,
            'period' => $period
        ];

        $this->view('reports/profit', $data);
    }

    /**
     * Export report to CSV
     */
    public function export()
    {
        $type = $_GET['type'] ?? 'sales';
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'sales':
                $this->exportSalesReport($output, $startDate, $endDate);
                break;
            case 'inventory':
                $this->exportInventoryReport($output);
                break;
            case 'customers':
                $this->exportCustomersReport($output);
                break;
        }

        fclose($output);
    }

    /**
     * Export sales report to CSV
     */
    private function exportSalesReport($output, $startDate, $endDate)
    {
        fputcsv($output, ['Sale ID', 'Date', 'Customer', 'Total Amount', 'Payment Method']);

        $sales = $this->reportModel->getSalesReport($startDate, $endDate);
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->sale_id,
                $sale->sale_date,
                $sale->customer_name,
                $sale->total_amount,
                $sale->payment_method
            ]);
        }
    }

    /**
     * Export inventory report to CSV
     */
    private function exportInventoryReport($output)
    {
        fputcsv($output, ['Product ID', 'Product Name', 'Inventory', 'Min Inventory', 'Unit Price', 'Total Value']);

        $inventory = $this->reportModel->getInventoryReport();
        foreach ($inventory as $item) {
            fputcsv($output, [
                $item->product_id,
                $item->product_name,
                $item->Inventory,
                $item->minimum_Inventory,
                $item->unit_price,
                $item->total_value
            ]);
        }
    }

    /**
     * Export customers report to CSV
     */
    private function exportCustomersReport($output)
    {
        fputcsv($output, ['Customer ID', 'Name', 'Email', 'Phone', 'Total Purchases', 'Credit Limit', 'Current Balance']);

        $customers = $this->reportModel->getCustomerAnalysis(365);
        foreach ($customers as $customer) {
            fputcsv($output, [
                $customer->customer_id,
                $customer->customer_name,
                $customer->email,
                $customer->phone,
                $customer->total_sales,
                $customer->credit_limit,
                $customer->current_balance
            ]);
        }
    }

    public function purchases()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
            $to_date = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
            $purchases = $this->reportModel->getPurchaseReports($from_date, $to_date);
            if (!$purchases) {
                $purchases = [];
                flash('report_message', 'No purchases found for selected dates');
            }
            $data = [
                'purchases' => $purchases,
                'from_date' => $from_date,
                'to_date' => $to_date
            ];
            $this->view('reports/purchases', $data);
        } else {
            $this->view('reports/purchases');
        }
    }

    public function salereturns()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
            $to_date = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
            $salereturns = $this->reportModel->getSaleReturnReports($from_date, $to_date);
            if (!$salereturns) {
                $salereturns = [];
                flash('report_message', 'No sale returns found for selected dates');
            }
            $data = [
                'salereturns' => $salereturns,
                'from_date' => $from_date,
                'to_date' => $to_date
            ];
            $this->view('reports/salereturns', $data);
        } else {
            $this->view('reports/salereturns');
        }
    }

    public function purchasereturns()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
            $to_date = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
            $purchasereturns = $this->reportModel->getPurchaseReturnReports($from_date, $to_date);
            if (!$purchasereturns) {
                $purchasereturns = [];
                flash('report_message', 'No purchase returns found for selected dates');
            }
            $data = [
                'purchasereturns' => $purchasereturns,
                'from_date' => $from_date,
                'to_date' => $to_date
            ];
            $this->view('reports/purchasereturns', $data);
        } else {
            $this->view('reports/purchasereturns');
        }
    }

    /**
     * Customer insights
     */
    public function customers()
    {
        $customerAnalysis = $this->reportModel->getCustomerAnalysis(50);

        $data = [
            'title' => 'Customer Insights',
            'customer_analysis' => $customerAnalysis
        ];

        $this->view('reports/customers', $data);
    }

    /**
     * Profit margin analysis
     */
    public function profitability()
    {
        $profitMargins = $this->reportModel->getProfitMarginAnalysis();

        $data = [
            'title' => 'Profitability Analysis',
            'profit_margins' => $profitMargins
        ];

        $this->view('reports/profitability', $data);
    }
}
