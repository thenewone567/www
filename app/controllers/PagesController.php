<?php
class PagesController extends Controller
{
  public function __construct()
  {
    parent::__construct(); // Ensure parent constructor is called if needed
  }

  public function index()
  {
    if (isLoggedIn()) {
      redirect('dashboard');
    } else {
      redirect('users/login');
    }
  }

  public function about()
  {
    $data = [
      'title' => 'About Us',
      'description' => 'This is a hardware store management application.'
    ];

    $this->view('pages/about', $data);
  }
}
