<?php

class PurchasesController
{
    public function showNewPurchaseForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);
        require_once ROOT_PATH . 'views/new-purchase.php';
    }

    public function createPurchase()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Purchase.php';
        $purchaseModel = new Purchase();
        $purchaseModel->createPurchase($_POST);

        header('Location: /purchases/history');
    }

    public function showPurchasesHistory()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        require_once ROOT_PATH . 'models/Purchase.php';
        $purchaseModel = new Purchase();
        $purchases = $purchaseModel->getPurchasesHistory();

        require_once ROOT_PATH . 'views/purchases-history.php';
    }

    public function rateSupplier()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager']);

        // In a real application, you would update the supplier's rating in the database

        header('Location: /purchases/history');
    }
}
