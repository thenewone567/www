<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Dashboard - Hardware Store</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">
</head>

<body>
    <!-- Contractor Navigation -->
    <nav class="navbar theme-navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo URLROOT; ?>/contractor/dashboard">
                <strong>Hardware Store</strong> - Contractor Portal
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#contractorNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="contractorNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/contractor/dashboard">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/contractor/jobs">
                            <i class="fas fa-briefcase"></i> My Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/contractor/earnings">
                            <i class="fas fa-chart-line"></i> Earnings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/contractor/profile">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="contractorDropdown" role="button"
                            data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['contractor_name']; ?>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo URLROOT; ?>/contractor/profile">Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo URLROOT; ?>/contractor/logout">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                <?php endif; ?>

                <!-- Welcome Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="h3">Welcome back, <?php echo $_SESSION['contractor_name']; ?>!</h1>
                        <p class="text-muted">Manage your jobs, earnings, and profile from your contractor dashboard.
                        </p>
                    </div>
                </div>

                <!-- Dashboard Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-briefcase fa-2x text-primary mb-2"></i>
                                <h4>0</h4>
                                <p class="text-muted">Active Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h4>0</h4>
                                <p class="text-muted">Completed Jobs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                                <h4>$0.00</h4>
                                <p class="text-muted">This Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x text-warning mb-2"></i>
                                <h4>$0.00</h4>
                                <p class="text-muted">Total Earned</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Jobs</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">No recent jobs found.</p>
                                <a href="<?php echo URLROOT; ?>/contractor/jobs" class="btn btn-primary">View All
                                    Jobs</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="<?php echo URLROOT; ?>/contractor/jobs"
                                        class="list-group-item list-group-item-action">
                                        <i class="fas fa-briefcase mr-2"></i> View My Jobs
                                    </a>
                                    <a href="<?php echo URLROOT; ?>/contractor/earnings"
                                        class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-line mr-2"></i> View Earnings
                                    </a>
                                    <a href="<?php echo URLROOT; ?>/contractor/profile"
                                        class="list-group-item list-group-item-action">
                                        <i class="fas fa-user mr-2"></i> Update Profile
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-phone mr-2"></i> Contact Support
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills & Certifications -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">My Skills & Certifications</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Manage your skills and certifications to get better job matches.
                                </p>
                                <a href="<?php echo URLROOT; ?>/contractor/profile"
                                    class="btn btn-outline-primary">Update Skills</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>