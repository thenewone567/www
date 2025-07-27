<?php
class ReportsController extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->reportModel = $this->model('Report');
    }

    public function index(){
        $this->view('reports/index');
    }

    public function sales(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $from_date = trim($_POST['from_date']);
            $to_date = trim($_POST['to_date']);
            $sales = $this->reportModel->getSalesReports($from_date, $to_date);
            $data = [
                'sales' => $sales,
                'from_date' => $from_date,
                'to_date' => $to_date
            ];
            $this->view('reports/sales', $data);
        } else {
            $this->view('reports/sales');
        }
    }

    public function purchases(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $from_date = trim($_POST['from_date']);
            $to_date = trim($_POST['to_date']);
            $purchases = $this->reportModel->getPurchaseReports($from_date, $to_date);
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

    public function salereturns(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $from_date = trim($_POST['from_date']);
            $to_date = trim($_POST['to_date']);
            $salereturns = $this->reportModel->getSaleReturnReports($from_date, $to_date);
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

    public function purchasereturns(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $from_date = trim($_POST['from_date']);
            $to_date = trim($_POST['to_date']);
            $purchasereturns = $this->reportModel->getPurchaseReturnReports($from_date, $to_date);
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
}
