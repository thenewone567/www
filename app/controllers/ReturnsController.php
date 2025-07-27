<?php
class Returns extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->returnModel = $this->model('Return');
    }

    public function index(){
        $saleReturns = $this->returnModel->getSaleReturns();
        $purchaseReturns = $this->returnModel->getPurchaseReturns();
        $data = [
            'sale_returns' => $saleReturns,
            'purchase_returns' => $purchaseReturns
        ];
        $this->view('returns/index', $data);
    }

    public function addsale(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'sale_id' => trim($_POST['sale_id']),
                'return_date' => trim($_POST['return_date']),
                'reason' => trim($_POST['reason']),
                'refund_amount' => trim($_POST['refund_amount']),
                'sale_id_err' => '',
                'return_date_err' => ''
            ];

            // Validate sale id
            if(empty($data['sale_id'])){
                $data['sale_id_err'] = 'Please enter sale id';
            }
            // Validate return date
            if(empty($data['return_date'])){
                $data['return_date_err'] = 'Please enter return date';
            }

            if(empty($data['sale_id_err']) && empty($data['return_date_err'])){
                if($this->returnModel->addSaleReturn($data)){
                    flash('return_message', 'Sale Return Added');
                    redirect('returns');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('returns/addsale', $data);
            }
        } else {
            $data = [
                'sale_id' => '',
                'return_date' => '',
                'reason' => '',
                'refund_amount' => ''
            ];
            $this->view('returns/addsale', $data);
        }
    }

    public function addpurchase(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'purchase_id' => trim($_POST['purchase_id']),
                'return_date' => trim($_POST['return_date']),
                'reason' => trim($_POST['reason']),
                'purchase_id_err' => '',
                'return_date_err' => ''
            ];

            // Validate purchase id
            if(empty($data['purchase_id'])){
                $data['purchase_id_err'] = 'Please enter purchase id';
            }
            // Validate return date
            if(empty($data['return_date'])){
                $data['return_date_err'] = 'Please enter return date';
            }

            if(empty($data['purchase_id_err']) && empty($data['return_date_err'])){
                if($this->returnModel->addPurchaseReturn($data)){
                    flash('return_message', 'Purchase Return Added');
                    redirect('returns');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('returns/addpurchase', $data);
            }
        } else {
            $data = [
                'purchase_id' => '',
                'return_date' => '',
                'reason' => ''
            ];
            $this->view('returns/addpurchase', $data);
        }
    }
}
