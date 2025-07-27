<?php
  class Pages extends Controller {
    public function __construct(){

    }

    public function index(){
      if(isLoggedIn()){
        redirect('dashboard');
      } else {
        redirect('users/login');
      }
    }

    public function about(){
      $data = [
        'title' => 'About Us',
        'description' => 'This is a hardware store management application.'
      ];

      $this->view('pages/about', $data);
    }
  }
