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

        // Get supplier statistics for dashboard cards
        $stats = $this->supplierModel->getSupplierOverviewStats();

        $data = [
            'suppliers' => $suppliers,
            'total_suppliers' => $stats['total'],
            'active_suppliers' => $stats['active'],
            'pending_suppliers' => $stats['pending'],
            'onhold_suppliers' => $stats['onhold']
        ];
        $this->view('suppliers/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'supplier_name' => isset($_POST['supplier_name']) ? trim($_POST['supplier_name']) : '',
                'contact_person' => isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'gst_number' => isset($_POST['gst_number']) ? trim($_POST['gst_number']) : '',
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            } elseif ($this->supplierModel->isSupplierNameExists($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Supplier name already exists';
            }

            // Validate email format if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            } elseif (!empty($data['email']) && $this->supplierModel->isEmailExists($data['email'])) {
                $data['email_err'] = 'Email address already exists';
            }

            // Validate GST number for duplicates if provided
            if (!empty($data['gst_number']) && $this->supplierModel->isGstNumberExists($data['gst_number'])) {
                $data['gst_number_err'] = 'GST number already exists';
            }

            if (empty($data['supplier_name_err']) && empty($data['email_err']) && empty($data['gst_number_err'])) {
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
                'contact_person' => '',
                'phone' => '',
                'email' => '',
                'address' => '',
                'gst_number' => '',
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
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
                'contact_person' => isset($_POST['contact_person']) ? trim($_POST['contact_person']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'gst_number' => isset($_POST['gst_number']) ? trim($_POST['gst_number']) : '',
                'supplier_name_err' => '',
                'email_err' => '',
                'gst_number_err' => ''
            ];

            // Validate supplier name
            if (empty($data['supplier_name'])) {
                $data['supplier_name_err'] = 'Please enter supplier name';
            } elseif ($this->supplierModel->isSupplierNameExists($data['supplier_name'], $id)) {
                $data['supplier_name_err'] = 'Supplier name already exists';
            }

            // Validate email format if provided
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            }

            // Validate GST number for duplicates if provided
            if (!empty($data['gst_number']) && $this->supplierModel->isGstNumberExists($data['gst_number'], $id)) {
                $data['gst_number_err'] = 'GST number already exists';
            }

            if (empty($data['supplier_name_err']) && empty($data['email_err']) && empty($data['gst_number_err'])) {
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
                    'contact_person' => $supplier->contact_person ?? '',
                    'phone' => $supplier->phone ?? '',
                    'email' => $supplier->email ?? '',
                    'address' => $supplier->address ?? '',
                    'gst_number' => $supplier->gst_number ?? '',
                    'supplier_name_err' => '',
                    'email_err' => '',
                    'gst_number_err' => ''
                ];
            } else {
                $data = [
                    'id' => $id,
                    'supplier_name' => '',
                    'contact_person' => '',
                    'phone' => '',
                    'email' => '',
                    'address' => '',
                    'gst_number' => '',
                    'supplier_name_err' => '',
                    'email_err' => '',
                    'gst_number_err' => ''
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
