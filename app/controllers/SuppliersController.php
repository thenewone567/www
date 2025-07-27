<?php
class Suppliers extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->supplierModel = $this->model('Supplier');
    }

    public function index(){
        $suppliers = $this->supplierModel->getSuppliers();
        $data = [
            'suppliers' => $suppliers
        ];
        $this->view('suppliers/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'supplier_name' => trim($_POST['supplier_name']),
                'contact_info' => trim($_POST['contact_info']),
                'gst_info' => trim($_POST['gst_info']),
                'due_amount' => trim($_POST['due_amount']),
                'supplier_name_err' => ''
            ];

            // Validate supplier name
            if(empty($data['supplier_name'])){
                $data['supplier_name_err'] = 'Please enter supplier name';
            }

            if(empty($data['supplier_name_err'])){
                if($this->supplierModel->addSupplier($data)){
                    flash('supplier_message', 'Supplier Added');
                    redirect('suppliers');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('suppliers/add', $data);
            }
        } else {
            $data = [
                'supplier_name' => '',
                'contact_info' => '',
                'gst_info' => '',
                'due_amount' => ''
            ];
            $this->view('suppliers/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'supplier_name' => trim($_POST['supplier_name']),
                'contact_info' => trim($_POST['contact_info']),
                'gst_info' => trim($_POST['gst_info']),
                'due_amount' => trim($_POST['due_amount']),
                'supplier_name_err' => ''
            ];

            // Validate supplier name
            if(empty($data['supplier_name'])){
                $data['supplier_name_err'] = 'Please enter supplier name';
            }

            if(empty($data['supplier_name_err'])){
                if($this->supplierModel->updateSupplier($data)){
                    flash('supplier_message', 'Supplier Updated');
                    redirect('suppliers');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('suppliers/edit', $data);
            }
        } else {
            $supplier = $this->supplierModel->getSupplierById($id);
            $data = [
                'id' => $id,
                'supplier_name' => $supplier->supplier_name,
                'contact_info' => $supplier->contact_info,
                'gst_info' => $supplier->gst_info,
                'due_amount' => $supplier->due_amount
            ];
            $this->view('suppliers/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->supplierModel->deleteSupplier($id)){
                flash('supplier_message', 'Supplier Removed');
                redirect('suppliers');
            } else {
                die('Something went wrong');
            }
        } else {
            redirect('suppliers');
        }
    }
}
