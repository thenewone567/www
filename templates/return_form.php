<?php
require_once 'config/config.php';

// Fetch sales and purchases for dropdowns
$sales = $pdo->query("SELECT * FROM sales ORDER BY sale_id DESC")->fetchAll(PDO::FETCH_ASSOC);
$purchases = $pdo->query("SELECT * FROM purchases ORDER BY purchase_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>New Return</h2>

<form action="src/actions/create_return.php" method="post">
    <label for="return_type">Return Type:</label>
    <select name="return_type" id="return_type" required>
        <option value="sale">Sale Return (from customer)</option>
        <option value="purchase">Purchase Return (to supplier)</option>
    </select>
    <br>

    <div id="sale-return-fields">
        <label for="sale_id">Select Sale:</label>
        <select name="sale_id" id="sale_id">
            <option value="">Select Sale</option>
            <?php foreach ($sales as $sale): ?>
                <option value="<?= $sale['sale_id'] ?>">Sale #<?= $sale['sale_id'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="purchase-return-fields" style="display: none;">
        <label for="purchase_id">Select Purchase:</label>
        <select name="purchase_id" id="purchase_id">
            <option value="">Select Purchase</option>
            <?php foreach ($purchases as $purchase): ?>
                <option value="<?= $purchase['purchase_id'] ?>">Purchase #<?= $purchase['purchase_id'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <br>

    <label for="reason">Reason:</label>
    <textarea name="reason" id="reason" rows="3"></textarea>
    <br>

    <label for="status">Action:</label>
    <select name="status" id="status" required>
        <option value="restocked">Restock Item</option>
        <option value="written_off">Write-off Item</option>
    </select>
    <br>

    <button type="submit">Process Return</button>
</form>

<script>
document.getElementById('return_type').addEventListener('change', function() {
    if (this.value === 'sale') {
        document.getElementById('sale-return-fields').style.display = 'block';
        document.getElementById('purchase-return-fields').style.display = 'none';
    } else {
        document.getElementById('sale-return-fields').style.display = 'none';
        document.getElementById('purchase-return-fields').style.display = 'block';
    }
});
</script>
