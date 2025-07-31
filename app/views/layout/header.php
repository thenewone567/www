<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <title><?php echo SITENAME; ?></title>
</head>

<body>
    <div class="d-flex w-100 h-100" id="wrapper">
        <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'sidebar.php'; ?>
        <div id="page-content-wrapper" class="w-100 h-100">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?php echo URLROOT; ?>/admin" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Admin Settings
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>
                <?php endif; ?>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item">
                            <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownNotifications" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-bell"></i>
                                <?php if (isset($data) && isset($data['notifications']) && is_array($data['notifications']) && count($data['notifications']) > 0): ?>
                                    <span class="badge badge-danger"><?php echo count($data['notifications']); ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="navbarDropdownNotifications">
                                <?php if (isset($data) && isset($data['notifications']) && is_array($data['notifications']) && count($data['notifications']) > 0): ?>
                                    <?php foreach ($data['notifications'] as $notification): ?>
                                        <a class="dropdown-item" href="#"><?php echo $notification->message; ?></a>
                                    <?php endforeach; ?>
                                    <div class="dropdown-divider"></div>
                                <?php else: ?>
                                    <span class="dropdown-item text-muted">No notifications</span>
                                <?php endif; ?>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/notifications">View all
                                    notifications</a>
                            </div>
                        </li>
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile">Profile</a>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/changePassword">Change
                                    Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/logout">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="container-fluid pt-3 fade-in">