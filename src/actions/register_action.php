<?php
require_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $role_id]);

        // Redirect to login page
        header("Location: ../../templates/login.php?success=registration_successful");
        exit();
    } catch (PDOException $e) {
        // Handle username conflict
        if ($e->errorInfo[1] == 1062) {
            header("Location: ../../templates/register.php?error=username_taken");
            exit();
        } else {
            die("ERROR: Could not execute $sql. " . $e->getMessage());
        }
    }
}
?>
