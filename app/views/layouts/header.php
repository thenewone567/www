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
            const savedTheme = localStorage.getItem('preferred-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/theme-system.css">
    <title><?php echo SITENAME; ?></title>
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
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-user"></i>
                                <?php if (isset($_SESSION['display_name'])): ?>
                                    <span class="ml-1"><?php echo $_SESSION['display_name']; ?></span>
                                <?php elseif (isset($_SESSION['user_name'])): ?>
                                    <span class="ml-1"><?php echo ucfirst($_SESSION['user_name']); ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUser">
                                <?php if ((isset($_SESSION['display_name']) || isset($_SESSION['user_name'])) && isset($_SESSION['user_role'])): ?>
                                    <h6 class="dropdown-header">
                                        <i class="fas fa-user-circle"></i>
                                        <?php echo $_SESSION['display_name'] ?? ucfirst($_SESSION['user_name']); ?>
                                        <br><small class="text-muted">Role: <?php echo $_SESSION['user_role']; ?></small>
                                    </h6>
                                    <div class="dropdown-divider"></div>
                                <?php endif; ?>
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