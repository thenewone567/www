<?php

class UsersController
{
    public function index()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);

        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $users = $userModel->getUsers();

        require_once ROOT_PATH . 'views/users.php';
    }

    public function add()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);

        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $userModel->addUser($_POST);

        header('Location: /users');
    }

    public function edit()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);

        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $userModel->updateUser($_POST);

        header('Location: /users');
    }

    public function delete()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);

        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $userModel->deleteUser($_GET['id']);

        header('Location: /users');
    }

    public function showLoginActivity()
    {
        require_once ROOT_PATH . 'helpers/Auth.php';
        Auth::check(['Admin']);

        require_once ROOT_PATH . 'models/User.php';
        $userModel = new User();
        $loginActivity = $userModel->getLoginActivity();

        require_once ROOT_PATH . 'views/login-activity.php';
    }
}
