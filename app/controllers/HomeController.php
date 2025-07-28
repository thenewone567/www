<?php
class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct(); // Add if parent has a constructor
    }

    public function index()
    {
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            redirect('users/login');
        }
    }
}