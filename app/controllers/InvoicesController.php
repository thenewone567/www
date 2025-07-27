<?php
class Invoices extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->invoiceModel = $this->model('Invoice');
        $this->saleModel = $this->model('Sale');
        $this->customerModel = $this->model('Customer');
        $this->settingModel = $this->model('Setting');
    }

    public function index(){
        $invoices = $this->invoiceModel->getInvoices();
        $data = [
            'invoices' => $invoices
        ];
        $this->view('invoices/index', $data);
    }

    public function show($id){
        $invoice = $this->invoiceModel->getInvoiceById($id);
        $sale = $this->saleModel->getSaleById($invoice->sale_id);
        $saleItems = $this->saleModel->getSaleItemsBySaleId($invoice->sale_id);
        $customer = $this->customerModel->getCustomerById($sale->customer_id);
        $settings = $this->settingModel->getSettings();
        $data = [
            'invoice' => $invoice,
            'sale' => $sale,
            'saleItems' => $saleItems,
            'customer' => $customer,
            'settings' => $settings
        ];
        $this->view('invoices/show', $data);
    }

    public function generate($sale_id){
        // For simplicity, we'll just create a new invoice record
        // In a real application, you would have a more robust invoice numbering system
        $invoice_number = 'INV-' . date('Ymd') . '-' . $sale_id;
        $sale = $this->saleModel->getSaleById($sale_id);
        $data = [
            'sale_id' => $sale_id,
            'invoice_number' => $invoice_number,
            'total_amount' => $sale->total_amount,
            'tax_amount' => 0, // Assuming no tax for now
            'discount_amount' => 0 // Assuming no discount for now
        ];
        $invoice_id = $this->invoiceModel->addInvoice($data);
        if($invoice_id){
            redirect('invoices/show/' . $invoice_id);
        } else {
            die('Something went wrong');
        }
    }
}
