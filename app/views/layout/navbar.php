<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="darkSwitch">
                    <label class="custom-control-label" for="darkSwitch">Dark Mode</label>
                </div>
            </li>
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownNotifications" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-bell"></i>
                    <?php if (isset($data) && isset($data['notifications']) && is_array($data['notifications']) && count($data['notifications']) > 0): ?>
                        <span class="badge badge-danger"><?php echo count($data['notifications']); ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownNotifications">
                    <?php if (isset($data) && isset($data['notifications']) && is_array($data['notifications']) && count($data['notifications']) > 0): ?>
                        <?php foreach ($data['notifications'] as $notification): ?>
                            <a class="dropdown-item" href="#"><?php echo $notification->message; ?></a>
                        <?php endforeach; ?>
                        <div class="dropdown-divider"></div>
                    <?php else: ?>
                        <span class="dropdown-item text-muted">No notifications</span>
                    <?php endif; ?>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/notifications">View all notifications</a>
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
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/changePassword">Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/logout">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>