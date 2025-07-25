<?php

class InventoryController
{
    public function showReceivingForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);
        require_once ROOT_PATH . 'views/receiving.php';
    }

    public function receive()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);

        require_once ROOT_PATH . 'models/Inventory.php';
        $inventoryModel = new Inventory();
        $inventoryModel->receive($_POST);

        header('Location: /inventory/receiving');
    }

    public function showRestockForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);
        require_once ROOT_PATH . 'views/restock.php';
    }

    public function restock()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);

        require_once ROOT_PATH . 'models/Inventory.php';
        $inventoryModel = new Inventory();
        $inventoryModel->restock($_POST);

        header('Location: /inventory/restock');
    }

    public function showPutawayForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);
        require_once ROOT_PATH . 'views/putaway.php';
    }

    public function putaway()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);

        require_once ROOT_PATH . 'models/Inventory.php';
        $inventoryModel = new Inventory();
        $inventoryModel->putaway($_POST);

        header('Location: /inventory/putaway');
    }

    public function showCycleCountForm()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);
        require_once ROOT_PATH . 'views/cycle-count.php';
    }

    public function cycleCount()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);

        require_once ROOT_PATH . 'models/Inventory.php';
        $inventoryModel = new Inventory();
        $inventoryModel->cycleCount($_POST);

        header('Location: /inventory/cycle-count');
    }
}
