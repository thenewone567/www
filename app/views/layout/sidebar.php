<div class="bg-dark border-right text-light" id="sidebar-wrapper">
    <div class="sidebar-heading"><?php echo SITENAME; ?></div>
    <div class="list-group list-group-flush">
        <a href="<?php echo URLROOT; ?>/dashboard" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="<?php echo URLROOT; ?>/sales" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-chart-line"></i> Sales
        </a>
        <a href="<?php echo URLROOT; ?>/purchases" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-cart-shopping"></i> Purchases
        </a>
        <a href="<?php echo URLROOT; ?>/returns" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-undo"></i> Returns
        </a>
        <a href="<?php echo URLROOT; ?>/inventory" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-boxes-stacked"></i> Inventory
        </a>
        <a href="<?php echo URLROOT; ?>/cycle-counts" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fas fa-clipboard-list"></i> Cycle Counts
        </a>
        <a href="<?php echo URLROOT; ?>/reports" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-chart-pie"></i> Reports
        </a>
        <a href="<?php echo URLROOT; ?>/expenses" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-wallet"></i> Expenses
        </a>
        <a href="<?php echo URLROOT; ?>/settings" class="list-group-item list-group-item-action bg-dark text-light">
            <i class="fa-solid fa-cog"></i> Settings
        </a>
        <?php
        // Show Admin Panel only for admin users
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
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