<?php

class RegisterController
{
    public function showRegistrationForm()
    {
        require_once ROOT_PATH . 'views/register.php';
    }

    public function register()
    {
        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $userModel->addUser($_POST);

        header('Location: /login');
    }
}
