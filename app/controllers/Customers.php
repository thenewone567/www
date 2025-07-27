<?php
class Customers extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->customerModel = $this->model('Customer');
    }

    public function index(){
        $customers = $this->customerModel->getCustomers();
        $data = [
            'customers' => $customers
        ];
        $this->view('customers/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'customer_name' => trim($_POST['customer_name']),
                'contact_info' => trim($_POST['contact_info']),
                'credit_limit' => trim($_POST['credit_limit']),
                'customer_name_err' => ''
            ];

            // Validate customer name
            if(empty($data['customer_name'])){
                $data['customer_name_err'] = 'Please enter customer name';
            }

            if(empty($data['customer_name_err'])){
                if($this->customerModel->addCustomer($data)){
                    flash('customer_message', 'Customer Added');
                    redirect('customers');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('customers/add', $data);
            }
        } else {
            $data = [
                'customer_name' => '',
                'contact_info' => '',
                'credit_limit' => ''
            ];
            $this->view('customers/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'customer_name' => trim($_POST['customer_name']),
                'contact_info' => trim($_POST['contact_info']),
                'credit_limit' => trim($_POST['credit_limit']),
                'customer_name_err' => ''
            ];

            // Validate customer name
            if(empty($data['customer_name'])){
                $data['customer_name_err'] = 'Please enter customer name';
            }

            if(empty($data['customer_name_err'])){
                if($this->customerModel->updateCustomer($data)){
                    flash('customer_message', 'Customer Updated');
                    redirect('customers');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('customers/edit', $data);
            }
        } else {
            $customer = $this->customerModel->getCustomerById($id);
            $data = [
                'id' => $id,
                'customer_name' => $customer->customer_name,
                'contact_info' => $customer->contact_info,
                'credit_limit' => $customer->credit_limit
            ];
            $this->view('customers/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->customerModel->deleteCustomer($id)){
                flash('customer_message', 'Customer Removed');
                redirect('customers');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('customers');
        }
    }
}
