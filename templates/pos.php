<?php
require_once 'config/config.php';

// Fetch products for the dropdown
$products = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>New Sale</h2>

<form action="src/actions/create_sale.php" method="post">
    <div id="sale-items">
        <div class="sale-item">
            <select name="product_id[]" class="product-select">
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['product_id'] ?>" data-price="<?= $product['unit_price'] ?? 0 ?>">
                        <?= htmlspecialchars($product['product_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity[]" class="quantity" value="1" min="1">
            <span class="item-total">0.00</span>
        </div>
    </div>

    <button type="button" id="add-item">Add Another Item</button>
    <hr>

    <label for="payment_mode">Payment Mode:</label>
    <select name="payment_mode" id="payment_mode">
        <option value="Cash">Cash</option>
        <option value="UPI">UPI</option>
        <option value="Card">Card</option>
    </select>
    <br>

    <h3>Total: <span id="grand-total">0.00</span></h3>

    <button type="submit">Create Sale</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleItems = document.getElementById('sale-items');
    const addItemBtn = document.getElementById('add-item');
    const grandTotalEl = document.getElementById('grand-total');

    function calculateTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.sale-item').forEach(item => {
            const productSelect = item.querySelector('.product-select');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const quantity = parseInt(item.querySelector('.quantity').value) || 0;
            const itemTotal = price * quantity;
            item.querySelector('.item-total').textContent = itemTotal.toFixed(2);
            grandTotal += itemTotal;
        });
        grandTotalEl.textContent = grandTotal.toFixed(2);
    }

    saleItems.addEventListener('change', calculateTotal);
    addItemBtn.addEventListener('click', function() {
        const newItem = saleItems.firstElementChild.cloneNode(true);
        newItem.querySelector('select').selectedIndex = 0;
        newItem.querySelector('input').value = 1;
        newItem.querySelector('.item-total').textContent = '0.00';
        saleItems.appendChild(newItem);
    });

    calculateTotal();
});
</script>
