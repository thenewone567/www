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
        $saleModel->createSale($_POST);

        header('Location: /sales/history');
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
