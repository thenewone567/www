<?php
class InvoicesController extends Controller
{
    public $customerModel;
    public $invoiceModel;
    public $saleModel;
    public $settingModel;

    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
        $this->invoiceModel = $this->model('Invoice');
        $this->saleModel = $this->model('Sale');
        $this->customerModel = $this->model('Customer');
        $this->settingModel = $this->model('Setting');
    }

    public function index()
    {
        $invoices = $this->invoiceModel->getInvoices();
        if (!$invoices) {
            $invoices = [];
            flash('invoice_message', 'No invoices found');
        }
        $data = [
            'invoices' => $invoices
        ];
        $this->view('invoices/index', $data);
    }

    public function show($id)
    {
        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            flash('invoice_message', 'Invoice not found');
            redirect('invoices');
            return;
        }
        $sale = $this->saleModel->getSaleById($invoice->sale_id);
        if (!$sale) {
            flash('invoice_message', 'Sale not found');
            redirect('invoices');
            return;
        }
        $saleItems = $this->saleModel->getSaleItemsBySaleId($invoice->sale_id);
        $customer = $this->customerModel->getCustomerById($sale->customer_id);
        $settings = $this->settingModel->getSettings();
        $data = [
            'invoice' => $invoice,
            'sale' => $sale,
            'saleItems' => is_array($saleItems) ? $saleItems : [],
            'customer' => $customer,
            'settings' => $settings
        ];
        $this->view('invoices/show', $data);
    }

    public function generate($sale_id)
    {
        $sale = $this->saleModel->getSaleById($sale_id);
        if (!$sale) {
            flash('invoice_message', 'Sale not found');
            redirect('invoices');
            return;
        }
        $invoice_number = 'INV-' . date('Ymd') . '-' . $sale_id;
        $data = [
            'sale_id' => $sale_id,
            'invoice_number' => $invoice_number,
            'total_amount' => $sale->total_amount,
            'tax_amount' => 0, // Assuming no tax for now
            'discount_amount' => 0 // Assuming no discount for now
        ];
        $invoice_id = $this->invoiceModel->addInvoice($data);
        if ($invoice_id) {
            redirect('invoices/show/' . $invoice_id);
        } else {
            die('Something went wrong');
        }
    }
}
