<?php

class SalesController
{
    public function showNewSaleForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);
        require_once ROOT_PATH . 'views/new-sale.php';
    }

    public function createSale()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Sale.php';
        $saleModel = new Sale();
        $saleID = $saleModel->createSale($_POST);

        require_once ROOT_PATH . 'models/Invoice.php';
        $invoiceModel = new Invoice();
        $invoiceID = $invoiceModel->createInvoice($saleID);

        header('Location: /invoices/show/' . $invoiceID);
    }

    public function showSalesHistory()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Sale.php';
        $saleModel = new Sale();
        $sales = $saleModel->getSalesHistory();

        require_once ROOT_PATH . 'views/sales-history.php';
    }
}
