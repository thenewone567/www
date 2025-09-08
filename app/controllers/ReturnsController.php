<?php
class ReturnsController extends Controller
{
    public $returnModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->returnModel = $this->model('ReturnOrder');
    }

    public function index()
    {
        $saleReturns = $this->returnModel->getSaleReturns();
        if (!$saleReturns) {
            $saleReturns = [];
            flash('return_message', 'No sale returns found');
        }
        $purchaseReturns = $this->returnModel->getPurchaseReturns();
        if (!$purchaseReturns) {
            $purchaseReturns = [];
            flash('return_message', 'No purchase returns found');
        }

        // Also load cancelled purchase orders that should appear in returns
        $purchaseModel = $this->model('Purchase');
        $cancelledPurchases = $purchaseModel->getCancelledPurchases();
        if (!$cancelledPurchases) {
            $cancelledPurchases = [];
        }

        $data = [
            'sale_returns' => $saleReturns,
            'purchase_returns' => $purchaseReturns,
            'cancelled_purchases' => $cancelledPurchases
        ];
        $this->view('returns/index', $data);
    }

    public function addsale()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'sale_id' => isset($_POST['sale_id']) ? trim($_POST['sale_id']) : '',
                'return_date' => isset($_POST['return_date']) ? trim($_POST['return_date']) : '',
                'reason' => isset($_POST['reason']) ? trim($_POST['reason']) : '',
                'refund_amount' => isset($_POST['refund_amount']) ? trim($_POST['refund_amount']) : '',
                'sale_id_err' => '',
                'return_date_err' => ''
            ];

            // Validate sale id
            if (empty($data['sale_id'])) {
                $data['sale_id_err'] = 'Please enter sale id';
            }
            // Validate return date
            if (empty($data['return_date'])) {
                $data['return_date_err'] = 'Please enter return date';
            }

            if (empty($data['sale_id_err']) && empty($data['return_date_err'])) {
                if ($this->returnModel->addSaleReturn($data)) {
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

    public function addpurchase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = sanitizePost($_POST);
            $data = [
                'purchase_id' => isset($_POST['purchase_id']) ? trim($_POST['purchase_id']) : '',
                'return_date' => isset($_POST['return_date']) ? trim($_POST['return_date']) : '',
                'reason' => isset($_POST['reason']) ? trim($_POST['reason']) : '',
                'purchase_id_err' => '',
                'return_date_err' => ''
            ];

            // Validate purchase id
            if (empty($data['purchase_id'])) {
                $data['purchase_id_err'] = 'Please select a purchase order';
            } else {
                // Verify that the purchase exists and can be returned
                $purchaseModel = $this->model('Purchase');
                $purchase = $purchaseModel->getPurchaseById($data['purchase_id']);
                if (!$purchase) {
                    $data['purchase_id_err'] = 'Invalid purchase order selected';
                } elseif ($purchase->status === 'cancelled') {
                    $data['purchase_id_err'] = 'Cannot return a cancelled purchase order';
                }
            }

            // Validate return date
            if (empty($data['return_date'])) {
                $data['return_date_err'] = 'Please enter return date';
            } elseif (strtotime($data['return_date']) > time()) {
                $data['return_date_err'] = 'Return date cannot be in the future';
            }

            if (empty($data['purchase_id_err']) && empty($data['return_date_err'])) {
                if ($this->returnModel->addPurchaseReturn($data)) {
                    flash('return_message', 'Purchase Return Added Successfully', 'alert alert-success');
                    redirect('returns');
                } else {
                    flash('return_message', 'Error adding purchase return', 'alert alert-danger');
                    redirect('returns');
                }
            } else {
                // Get available purchases for the dropdown
                $purchaseModel = $this->model('Purchase');
                $data['available_purchases'] = $purchaseModel->getReturnablePurchases();
                $this->view('returns/addpurchase', $data);
            }
        } else {
            // Get available purchases for the dropdown
            $purchaseModel = $this->model('Purchase');
            $availablePurchases = $purchaseModel->getReturnablePurchases();

            $data = [
                'purchase_id' => '',
                'return_date' => date('Y-m-d'), // Default to today
                'reason' => '',
                'purchase_id_err' => '',
                'return_date_err' => '',
                'available_purchases' => $availablePurchases
            ];
            $this->view('returns/addpurchase', $data);
        }
    }
}
