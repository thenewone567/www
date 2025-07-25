<?php

class RegisterController
{
    public function showRegistrationForm()
    {
        require_once ROOT_PATH . 'views/register.php';
    }

    public function register()
    {
        require_once ROOT_PATH . 'models/Users.php';
        $userModel = new Users();
        $userModel->createUser($_POST);

        header('Location: /login');
    }
}
