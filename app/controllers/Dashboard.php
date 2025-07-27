<?php
class Dashboard extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
    }

    public function index(){
        $data = [
            'title' => 'Dashboard',
            'sales_today' => '0',
            'sales_week' => '0',
            'sales_month' => '0',
            'top_selling' => [],
            'low_stock' => []
        ];
        $this->view('dashboard/index', $data);
    }
}
