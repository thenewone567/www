<div class="bg-light border-right" id="sidebar-wrapper">
    <div class="sidebar-heading"><?php echo SITENAME; ?></div>
    <div class="list-group list-group-flush">
        <a href="<?php echo URLROOT; ?>/dashboard" class="list-group-item list-group-item-action bg-light"><i
                class="fa-solid fa-gauge"></i>
            Dashboard</a>
        <a href="#salesSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i
                class="fa-solid fa-chart-line"></i> Sales</a>
        <ul class="collapse list-unstyled" id="salesSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/sales/new" class="list-group-item list-group-item-action bg-light">New
                    Sale</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/sales/list"
                    class="list-group-item list-group-item-action bg-light">Sales List</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/sales/invoices"
                    class="list-group-item list-group-item-action bg-light">Invoices</a>
            </li>
        </ul>
        <a href="#purchasesSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i
                class="fa-solid fa-cart-shopping"></i> Purchases</a>
        <ul class="collapse list-unstyled" id="purchasesSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/purchases/new"
                    class="list-group-item list-group-item-action bg-light">New Purchase</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/purchases/list"
                    class="list-group-item list-group-item-action bg-light">Purchase List</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/vendors/list"
                    class="list-group-item list-group-item-action bg-light">Vendor List</a>
            </li>
        </ul>
        <a href="#returnsSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i class="fa-solid fa-undo"></i>
            Returns</a>
        <ul class="collapse list-unstyled" id="returnsSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/returns/sale"
                    class="list-group-item list-group-item-action bg-light">Sale Returns</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/returns/purchase"
                    class="list-group-item list-group-item-action bg-light">Purchase Returns</a>
            </li>
        </ul>
        <a href="#inventorySubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i
                class="fa-solid fa-boxes-stacked"></i> Inventory</a>
        <ul class="collapse list-unstyled" id="inventorySubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/products"
                    class="list-group-item list-group-item-action bg-light">Products</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/categories"
                    class="list-group-item list-group-item-action bg-light">Categories</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/brands"
                    class="list-group-item list-group-item-action bg-light">Brands</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/units" class="list-group-item list-group-item-action bg-light">Units</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/inventory/lowstock"
                    class="list-group-item list-group-item-action bg-light">Low Stock Alerts</a>
            </li>
        </ul>
        <a href="#warehouseSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i
                class="fa-solid fa-warehouse"></i> Warehouse & Stock</a>
        <ul class="collapse list-unstyled" id="warehouseSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/warehouse/receiving"
                    class="list-group-item list-group-item-action bg-light">Receiving</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/warehouse/putaway"
                    class="list-group-item list-group-item-action bg-light">Putaway</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/warehouse/transfer"
                    class="list-group-item list-group-item-action bg-light">Stock Transfer</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/warehouse/cyclecount"
                    class="list-group-item list-group-item-action bg-light">Cycle Count</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/warehouse/locations"
                    class="list-group-item list-group-item-action bg-light">Warehouse Locations</a>
            </li>
        </ul>
        <a href="#reportsSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i
                class="fa-solid fa-chart-pie"></i> Reports</a>
        <ul class="collapse list-unstyled" id="reportsSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/reports/daily"
                    class="list-group-item list-group-item-action bg-light">Daily Report</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reports/gst" class="list-group-item list-group-item-action bg-light">GST
                    Report</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reports/profitloss"
                    class="list-group-item list-group-item-action bg-light">Profit/Loss</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/reports/stocksummary"
                    class="list-group-item list-group-item-action bg-light">Stock Summary</a>
            </li>
        </ul>
        <a href="#expensesSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i class="fa-solid fa-wallet"></i>
            Expenses</a>
        <ul class="collapse list-unstyled" id="expensesSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/expenses/add"
                    class="list-group-item list-group-item-action bg-light">Add Expense</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/expenses/categories"
                    class="list-group-item list-group-item-action bg-light">Expense Categories</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/expenses/reports"
                    class="list-group-item list-group-item-action bg-light">Expense Reports</a>
            </li>
        </ul>
        <a href="#settingsSubmenu" data-toggle="collapse" aria-expanded="false"
            class="list-group-item list-group-item-action bg-light dropdown-toggle"><i class="fa-solid fa-cog"></i>
            Settings</a>
        <ul class="collapse list-unstyled" id="settingsSubmenu">
            <li>
                <a href="<?php echo URLROOT; ?>/settings/users"
                    class="list-group-item list-group-item-action bg-light">User Management</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/settings/roles"
                    class="list-group-item list-group-item-action bg-light">Roles & Permissions</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/settings/shopinfo"
                    class="list-group-item list-group-item-action bg-light">Shop/Warehouse Info</a>
            </li>
            <li>
                <a href="<?php echo URLROOT; ?>/settings/tax"
                    class="list-group-item list-group-item-action bg-light">Tax Settings</a>
            </li>
        </ul>
        <a href="<?php echo URLROOT; ?>/users/logout" class="list-group-item list-group-item-action bg-light">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</div>