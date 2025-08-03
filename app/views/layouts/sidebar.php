<?php
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'SidebarHelper.php';
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
$roleId = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
$sidebarItems = getSidebarItems($userRole, $roleId);
?>
<div class="bg-dark border-right text-light" id="sidebar-wrapper">
    <div class="sidebar-heading"><?php echo SITENAME; ?></div>
    <div class="list-group list-group-flush">
        <?php foreach ($sidebarItems as $item): ?>
            <a href="<?php echo URLROOT . '/' . $item['url']; ?>"
                class="list-group-item list-group-item-action bg-dark text-light">
                <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>
        <?php if (isAdmin()): ?>
            <a href="<?php echo URLROOT; ?>/admin"
                class="list-group-item list-group-item-action bg-dark text-light admin-link">
                <i class="fa-solid fa-shield-alt"></i> Admin Panel
            </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/users/logout" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>