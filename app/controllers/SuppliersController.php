<?php
class SuppliersController extends Controller
{
    public $supplierModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->supplierModel = $this->model('Supplier');
    }

    public function index()
    {
        $suppliers = $this->supplierModel->getSuppliers();
        if (!$suppliers) {
            $suppliers = [];
            flash('supplier_message', 'No suppliers found');
        }
        $data = [
            'suppliers' => $suppliers
        ];
        $this->view('suppliers/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'supplier_name' => isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '',
                'contact_info' => isset($_POST['contact_info']) ? trim($_POST['contact_info']) : '',
                'gst_info' => isset($_POST['gst_info']) ? trim($_POST['gst_info']) : '',
                'due_amount' => isset($_POST['due_amount']) ? trim($_POST['due_amount']) : '',
                'supplier_name_err' => '',
                'due_amount_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            }
            // Validate due amount (optional, but recommended)
            if (!empty($data['due_amount']) && !is_numeric($data['due_amount'])) {
                $data['due_amount_err'] = 'Due amount must be a number';
            }

            if (empty($data['supplier_name_err']) && empty($data['due_amount_err'])) {
                if ($this->supplierModel->addSupplier($data)) {
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
                'due_amount' => '',
                'supplier_name_err' => '',
                'due_amount_err' => ''
            ];
            $this->view('suppliers/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'id' => $id,
                'supplier_name' => isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '',
                'contact_info' => isset($_POST['contact_info']) ? trim($_POST['contact_info']) : '',
                'gst_info' => isset($_POST['gst_info']) ? trim($_POST['gst_info']) : '',
                'due_amount' => isset($_POST['due_amount']) ? trim($_POST['due_amount']) : '',
                'supplier_name_err' => '',
                'due_amount_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            }
            // Validate due amount
            if (!empty($data['due_amount']) && !is_numeric($data['due_amount'])) {
                $data['due_amount_err'] = 'Due amount must be a number';
            }

            if (empty($data['supplier_name_err']) && empty($data['due_amount_err'])) {
                if ($this->supplierModel->updateSupplier($data)) {
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
            if ($supplier) {
                $data = [
                    'id' => $id,
                    'supplier_name' => $supplier->supplier_name,
                    'contact_info' => $supplier->contact_info,
                    'gst_info' => $supplier->gst_info,
                    'due_amount' => $supplier->due_amount,
                    'supplier_name_err' => '',
                    'due_amount_err' => ''
                ];
            } else {
                $data = [
                    'id' => $id,
                    'supplier_name' => '',
                    'contact_info' => '',
                    'gst_info' => '',
                    'due_amount' => '',
                    'supplier_name_err' => '',
                    'due_amount_err' => ''
                ];
                flash('supplier_message', 'Supplier not found');
            }
            $this->view('suppliers/edit', $data);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->supplierModel->deleteSupplier($id)) {
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
