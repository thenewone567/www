<?php
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'SidebarHelper.php';
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
$roleId = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : null;
$sidebarItems = getSidebarItems($userRole, $roleId);
?>
<div class="theme-sidebar border-right" id="sidebar-wrapper">
    <div class="sidebar-heading d-flex align-items-center" style="gap:8px;">
        <?php $logoPath = company_logo();
        if ($logoPath): ?>
            <img src="<?php echo URLROOT . '/' . htmlspecialchars($logoPath); ?>" alt="Logo"
                style="max-height:34px;max-width:48px;object-fit:contain;display:block;">
        <?php endif; ?>
        <span class="brand-title" style="font-weight:600;font-size:1.05rem;line-height:1.1;letter-spacing:.5px;">
            <?php echo htmlspecialchars(company_name()); ?>
        </span>
    </div>
    <div class="list-group list-group-flush">
        <?php foreach ($sidebarItems as $item): ?>
            <a href="<?php echo URLROOT . '/' . $item['url']; ?>"
                class="list-group-item list-group-item-action theme-sidebar-item">
                <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>
        <?php if (isAdmin()): ?>
            <a href="<?php echo URLROOT; ?>/admin"
                class="list-group-item list-group-item-action theme-sidebar-item admin-link">
                <i class="fa-solid fa-shield-alt"></i> Admin Panel
            </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/users/logout" class="list-group-item list-group-item-action theme-sidebar-item">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>