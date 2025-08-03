<?php
class StockController extends Controller
{
    public $stockModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->stockModel = $this->model('Stock');
    }

    public function index()
    {
        $stock = $this->stockModel->getStock();
        if (!$stock) {
            $stock = [];
            flash('stock_message', 'No stock found');
        }
        $movements = $this->stockModel->getStockMovements();
        if (!$movements) {
            $movements = [];
            flash('stock_message', 'No stock movements found');
        }
        $locations = $this->stockModel->getWarehouseLocations();
        if (!$locations) {
            $locations = [];
            flash('stock_message', 'No warehouse locations found');
        }
        $data = [
            'stock' => $stock,
            'movements' => $movements,
            'locations' => $locations
        ];
        $this->view('stock/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'product_id' => isset($_POST['product_id']) ? trim($_POST['product_id']) : '',
                'batch_number' => isset($_POST['batch_number']) ? trim($_POST['batch_number']) : '',
                'expiry_date' => isset($_POST['expiry_date']) ? trim($_POST['expiry_date']) : '',
                'quantity' => isset($_POST['quantity']) ? trim($_POST['quantity']) : '',
                'location_id' => isset($_POST['location_id']) ? trim($_POST['location_id']) : '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];

            // Validate product id
            if (empty($data['product_id'])) {
                $data['product_id_err'] = 'Please enter product id';
            }
            // Validate quantity
            if (empty($data['quantity'])) {
                $data['quantity_err'] = 'Please enter quantity';
            } elseif (!is_numeric($data['quantity'])) {
                $data['quantity_err'] = 'Quantity must be a number';
            }

            if (empty($data['product_id_err']) && empty($data['quantity_err'])) {
                if ($this->stockModel->addStock($data)) {
                    flash('stock_message', 'Stock Added');
                    redirect('stock');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('stock/add', $data);
            }
        } else {
            $data = [
                'product_id' => '',
                'batch_number' => '',
                'expiry_date' => '',
                'quantity' => '',
                'location_id' => ''
            ];
            $this->view('stock/add', $data);
        }
    }

    public function move()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'product_id' => isset($_POST['product_id']) ? trim($_POST['product_id']) : '',
                'from_location_id' => isset($_POST['from_location_id']) ? trim($_POST['from_location_id']) : '',
                'to_location_id' => isset($_POST['to_location_id']) ? trim($_POST['to_location_id']) : '',
                'quantity' => isset($_POST['quantity']) ? trim($_POST['quantity']) : '',
                'product_id_err' => '',
                'quantity_err' => ''
            ];

            // Validate product id
            if (empty($data['product_id'])) {
                $data['product_id_err'] = 'Please enter product id';
            }
            // Validate quantity
            if (empty($data['quantity'])) {
                $data['quantity_err'] = 'Please enter quantity';
            } elseif (!is_numeric($data['quantity'])) {
                $data['quantity_err'] = 'Quantity must be a number';
            }

            if (empty($data['product_id_err']) && empty($data['quantity_err'])) {
                if ($this->stockModel->addStockMovement($data)) {
                    flash('stock_message', 'Stock Moved');
                    redirect('stock');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('stock/move', $data);
            }
        } else {
            $data = [
                'product_id' => '',
                'from_location_id' => '',
                'to_location_id' => '',
                'quantity' => ''
            ];
            $this->view('stock/move', $data);
        }
    }

    public function locations()
    {
        $locations = $this->stockModel->getWarehouseLocations();
        if (!$locations) {
            $locations = [];
            flash('stock_message', 'No warehouse locations found');
        }
        $data = [
            'locations' => $locations
        ];
        $this->view('stock/locations', $data);
    }

    public function addlocation()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'location_name' => isset($_POST['location_name']) ? trim($_POST['location_name']) : '',
                'rack' => isset($_POST['rack']) ? trim($_POST['rack']) : '',
                'shelf' => isset($_POST['shelf']) ? trim($_POST['shelf']) : '',
                'location_name_err' => ''
            ];

            // Validate location name
            if (empty($data['location_name'])) {
                $data['location_name_err'] = 'Please enter location name';
            }

            if (empty($data['location_name_err'])) {
                if ($this->stockModel->addWarehouseLocation($data)) {
                    flash('stock_message', 'Location Added');
                    redirect('stock/locations');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('stock/addlocation', $data);
            }
        } else {
            $data = [
                'location_name' => '',
                'rack' => '',
                'shelf' => ''
            ];
            $this->view('stock/addlocation', $data);
        }
    }
}
