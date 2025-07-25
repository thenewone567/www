<?php

class User
{
    private $conn;

    public function __construct()
    {
        // For now, we will not connect to the database
        // require_once ROOT_PATH . 'config/database.php';
        // $this->conn = connect();
    }

    public function getUsers()
    {
        // This is a dummy implementation
        return [
            [
                'UserID' => 1,
                'Username' => 'admin',
                'Role' => 'Admin',
                'LastLogin' => '2024-07-25 12:00:00'
            ],
            [
                'UserID' => 2,
                'Username' => 'manager',
                'Role' => 'Manager',
                'LastLogin' => '2024-07-25 11:00:00'
            ]
        ];
    }

    public function addUser($data)
    {
        // This is a dummy implementation
        // In a real application, you would insert the user into the database
        return true;
    }

    public function updateUser($data)
    {
        // This is a dummy implementation
        // In a real application, you would update the user in the database
        return true;
    }

    public function deleteUser($id)
    {
        // This is a dummy implementation
        // In a real application, you would delete the user from the database
        return true;
    }

    public function getLoginActivity()
    {
        // This is a dummy implementation
        return [
            [
                'Username' => 'admin',
                'LoginTime' => '2024-07-25 12:00:00',
                'IPAddress' => '127.0.0.1'
            ],
            [
                'Username' => 'manager',
                'LoginTime' => '2024-07-25 11:00:00',
                'IPAddress' => '127.0.0.1'
            ]
        ];
    }
}
