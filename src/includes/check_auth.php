<?php
require_once __DIR__ . '/../../config/config.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"])) {
    header("location: ../templates/login.php");
    exit;
}
?>
