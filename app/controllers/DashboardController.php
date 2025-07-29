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
        $salesToday = $this->dashboardModel->getSalesToday();
        $salesWeek = $this->dashboardModel->getSalesWeek();
        $salesMonth = $this->dashboardModel->getSalesMonth();
        $topSelling = $this->dashboardModel->getTopSellingProducts();
        $lowStock = $this->dashboardModel->getLowStockProducts();

        // Defensive checks for null/false returns
        $data = [
            'title' => 'Dashboard',
            'sales_today' => $salesToday !== null ? $salesToday : 0,
            'sales_week' => $salesWeek !== null ? $salesWeek : 0,
            'sales_month' => $salesMonth !== null ? $salesMonth : 0,
            'top_selling' => is_array($topSelling) ? $topSelling : [],
            'low_stock' => is_array($lowStock) ? $lowStock : []
        ];
        $this->view('dashboard/index', $data);
    }
}
