<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Prevent flash of white content by applying dark theme immediately -->
    <script>
        // Apply theme immediately before any content renders
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Unified CSS - Ultra lean 4.8KB (91% smaller!) -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/app-unified.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Load jQuery early to prevent $ is not defined errors -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT"
        crossorigin="anonymous"></script>

    <title><?php echo htmlspecialchars(company_name()); ?></title>
</head>

<body>
    <div class="d-flex w-100 h-100" id="wrapper">
        <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'sidebar.php'; ?>
        <div id="page-content-wrapper" class="w-100 h-100">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg theme-navbar border-bottom">

                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Date and Time Display -->
                    <div class="navbar-text mr-auto">
                        <i class="fas fa-calendar-alt text-primary mr-2"></i>
                        <span id="currentDateTime" class="font-weight-medium"></span>
                    </div>

                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <!-- Admin Settings button removed from header -->
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
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                id="navbarDropdownUser" role="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <?php
                                // Get user profile picture if available
                                $profilePicture = $_SESSION['profile_picture'] ?? '';
                                if (!empty($profilePicture)): ?>
                                    <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile" class="mr-2"
                                        style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff; border-radius: 50%;">
                                <?php else: ?>
                                    <img src="<?php echo URLROOT; ?>/storage/uploads/users/avatar.png" alt="Default Avatar"
                                        class="mr-2"
                                        style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff; border-radius: 50%;">
                                <?php endif; ?>
                                <?php if (isset($_SESSION['display_name'])): ?>
                                    <span><?php echo $_SESSION['display_name']; ?></span>
                                <?php elseif (isset($_SESSION['user_name'])): ?>
                                    <span><?php echo ucfirst($_SESSION['user_name']); ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                                <?php if ((isset($_SESSION['display_name']) || isset($_SESSION['user_name'])) && isset($_SESSION['user_role'])): ?>
                                    <h6 class="dropdown-header">
                                        <?php echo $_SESSION['display_name'] ?? ucfirst($_SESSION['user_name']); ?>
                                    </h6>
                                    <div class="dropdown-divider"></div>
                                <?php endif; ?>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-shield-alt mr-2"></i>Role: <?php echo $_SESSION['user_role']; ?>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/changePassword">
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/logout">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="container-fluid pt-3 fade-in">

                <script>
                    // Update date and time every second
                    function updateDateTime() {
                        const now = new Date();
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        };

                        const dateTimeString = now.toLocaleDateString('en-US', options);
                        document.getElementById('currentDateTime').textContent = dateTimeString;
                    }

                    // Update immediately and then every second
                    updateDateTime();
                    setInterval(updateDateTime, 1000);
                </script>

                <!-- Make URLROOT available to JavaScript -->
                <script>
                    const URLROOT = '<?php echo URLROOT; ?>';
                </script>

                <!-- Theme Controller Script -->
                <script src="<?php echo URLROOT; ?>/public/js/theme-controller.js"></script>

                <!-- Basic UI Interactions -->
                <script>
                    // Simple dropdown functionality
                    document.addEventListener('DOMContentLoaded', function () {
                        // Handle dropdown toggles
                        document.querySelectorAll('[data-toggle="dropdown"]').forEach(function (toggle) {
                            toggle.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                // Close all other dropdowns
                                document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                                    if (menu !== toggle.nextElementSibling) {
                                        menu.style.display = 'none';
                                    }
                                });

                                // Toggle current dropdown
                                const menu = toggle.nextElementSibling;
                                if (menu && menu.classList.contains('dropdown-menu')) {
                                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                                }
                            });
                        });

                        // Handle navbar collapse toggle
                        document.querySelectorAll('[data-toggle="collapse"]').forEach(function (toggle) {
                            toggle.addEventListener('click', function (e) {
                                e.preventDefault();
                                const target = document.querySelector(toggle.getAttribute('data-target'));
                                if (target) {
                                    target.style.display = target.style.display === 'block' ? 'none' : 'block';
                                }
                            });
                        });

                        // Close dropdowns when clicking outside
                        document.addEventListener('click', function () {
                            document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                                menu.style.display = 'none';
                            });
                        });

                        // Handle sidebar toggle on mobile
                        const sidebarToggle = document.querySelector('.navbar-toggler');
                        const sidebar = document.querySelector('.theme-sidebar');

                        if (sidebarToggle && sidebar) {
                            sidebarToggle.addEventListener('click', function () {
                                sidebar.classList.toggle('show');
                            });
                        }

                        // Active page highlighting
                        const currentPath = window.location.pathname;
                        document.querySelectorAll('.theme-sidebar-item').forEach(function (link) {
                            const linkPath = new URL(link.href).pathname;
                            // Exact match for active state
                            if (currentPath === linkPath ||
                                (currentPath + '/' === linkPath) ||
                                (currentPath === linkPath + '/')) {
                                link.classList.add('active');
                            }
                        });
                    });
                </script>