<?php

class DashboardController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin', 'Manager', 'Supervisor', 'Warehouse Associate']);
        require_once ROOT_PATH . 'views/dashboard.php';
    }
}
