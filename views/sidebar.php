<div class="list-group">
    <a href="/dashboard" class="list-group-item list-group-item-action active">
        Dashboard
    </a>
    <?php
    $user = Session::get('user');
    if ($user) {
        if ($user['role'] === 'Admin') {
            ?>
            <a href="#" class="list-group-item list-group-item-action">Users</a>
            <a href="#" class="list-group-item list-group-item-action">Suppliers</a>
            <a href="#" class="list-group-item list-group-item-action">Products</a>
            <a href="#" class="list-group-item list-group-item-action">Customers</a>
            <?php
        } elseif ($user['role'] === 'Manager') {
            ?>
            <a href="#" class="list-group-item list-group-item-action">Products</a>
            <a href="/sales/new" class="list-group-item list-group-item-action">New Sale</a>
            <a href="/sales/history" class="list-group-item list-group-item-action">Sales History</a>
            <a href="#" class="list-group-item list-group-item-action">Purchases</a>
            <?php
        } elseif ($user['role'] === 'Supervisor') {
            ?>
            <a href="#" class="list-group-item list-group-item-action">Inventory</a>
            <a href="#" class="list-group-item list-group-item-action">Stock Management</a>
            <?php
        } elseif ($user['role'] === 'Warehouse Associate') {
            ?>
            <a href="#" class="list-group-item list-group-item-action">Inventory</a>
            <?php
        }
    }
    ?>
</div>
