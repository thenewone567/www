<?php
// app/controllers/HomeController.php

class HomeController extends Controller {
    public function index() {
        if(isLoggedIn()){
            redirect('dashboard');
        } else {
            redirect('users/login');
        }
    }
}
