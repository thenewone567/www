<?php

class LoginController
{
    public function showLoginForm()
    {
        require_once ROOT_PATH . 'views/login.php';
    }

    public function login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        require_once ROOT_PATH . 'models/Users.php';
        $userModel = new Users();
        $user = $userModel->getUserByUsername($username);

        require_once ROOT_PATH . 'helpers/Session.php';
        Session::start();

        if ($user && password_verify($password, $user['Password'])) {
            Session::set('user', [
                'username' => $user['Username'],
                'role' => $user['Role']
            ]);
            header('Location: /');
        } else {
            header('Location: /login');
        }
    }

    public function logout()
    {
        require_once ROOT_PATH . 'helpers/Session.php';
        Session::start();
        Session::destroy();
        header('Location: /login');
    }
}
