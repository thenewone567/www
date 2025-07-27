<?php require APPROOT . DS . 'app' . DS . views/layout/header.php'; ?>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                Products
            </div>
            <div class="card-body" id="product-list">
                <div class="row">
                <?php foreach($data['products'] as $product) : ?>
                    <div class="col-md-4">
                        <div class="card product-card" data-id="<?php echo $product->product_id; ?>" data-name="<?php echo $product->product_name; ?>" data-price="<?php echo $product->unit_price; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product->product_name; ?></h5>
                                <p class="card-text">$<?php echo $product->unit_price; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                Purchase Order
            </div>
            <div class="card-body">
                <form action="<?php echo URLROOT; ?>/purchases/add" method="post">
                    <table class="table" id="purchase-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <hr>
                    <h4>Total: $<span id="total-amount">0.00</span></h4>
                    <input type="hidden" name="total_amount" id="total_amount_input">
                    <div class="form-group">
                        <label for="supplier_id">Supplier:</label>
                        <input type="text" name="supplier_id" class="form-control" placeholder="Enter Supplier ID">
                    </div>
                    <input type="submit" value="Create Purchase Order" class="btn btn-success btn-block">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const productCards = document.querySelectorAll('.product-card');
    const purchaseTableBody = document.querySelector('#purchase-table tbody');
    const totalAmountSpan = document.getElementById('total-amount');
    const totalAmountInput = document.getElementById('total_amount_input');
    let purchaseItems = [];

    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const productId = card.dataset.id;
            const productName = card.dataset.name;
            const productPrice = parseFloat(card.dataset.price);

            const existingProduct = purchaseItems.find(item => item.id === productId);

            if(existingProduct){
                existingProduct.quantity++;
            } else {
                purchaseItems.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1
                });
            }
            updatePurchaseOrder();
        });
    });

    function updatePurchaseOrder(){
        purchaseTableBody.innerHTML = '';
        let total = 0;
        purchaseItems.forEach((item, index) => {
            const itemTotal = item.quantity * item.price;
            total += itemTotal;
            const row = `
                <tr>
                    <td>${item.name}</td>
                    <td><input type="number" class="form-control quantity-input" data-index="${index}" value="${item.quantity}" min="1"></td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>${itemTotal.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-item" data-index="${index}">X</button></td>
                </tr>
            `;
            purchaseTableBody.innerHTML += row;
        });
        totalAmountSpan.textContent = total.toFixed(2);
        totalAmountInput.value = total.toFixed(2);

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const index = e.target.dataset.index;
                const newQuantity = parseInt(e.target.value);
                purchaseItems[index].quantity = newQuantity;
                updatePurchaseOrder();
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = e.target.dataset.index;
                purchaseItems.splice(index, 1);
                updatePurchaseOrder();
            });
        });
    }
</script>
<?php require APPROOT . DS . 'app' . DS . views/layout/footer.php'; ?>
