<?php

class User
{
    private $conn;

    public function __construct()
    {
        require_once ROOT_PATH . 'config/database.php';
        $this->conn = connect();
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
        $username = $data['username'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'];

        $sql = "INSERT INTO Users (Username, Password, Role) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $role);

        return $stmt->execute();
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

    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM Users WHERE Username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
