<?php
class PosController extends Controller
{
    public function __construct()
    {
        if (!isLoggedIn()) {
            redirect('users/login');
        }
    }

    public function index()
    {
        // Redirect to the POS system in SalesController
        redirect('sales/pos');
    }
}
