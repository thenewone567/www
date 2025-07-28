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

        $data = [
            'title' => 'Dashboard',
            'sales_today' => $salesToday,
            'sales_week' => $salesWeek,
            'sales_month' => $salesMonth,
            'top_selling' => $topSelling,
            'low_stock' => $lowStock
        ];
        $this->view('dashboard/index', $data);
    }
}
