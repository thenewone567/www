-- Demo Products
INSERT INTO products (product_id, product_name, sku, category_id, brand_id, unit_id, purchase_price, selling_price, min_stock_level, reorder_level, is_active) VALUES
(1, 'Hammer', 'HAM001', 1, 1, 1, 5.00, 8.00, 10, 20, 1),
(2, 'Screwdriver', 'SCR002', 1, 1, 1, 3.00, 6.00, 15, 30, 1),
(3, 'Wrench', 'WRE003', 2, 2, 1, 7.00, 12.00, 8, 16, 1);

-- Demo Customers
INSERT INTO customers (customer_id, customer_name, contact_info, credit_limit) VALUES
(1, 'John Smith', 'john@example.com', 500),
(2, 'Jane Doe', 'jane@example.com', 300);

-- Demo Sales
INSERT INTO sales (sale_id, customer_id, total_amount, sale_date) VALUES
(1, 1, 24.00, CURDATE()),
(2, 2, 36.00, CURDATE()-INTERVAL 1 DAY);

-- Demo Stock
INSERT INTO stock (stock_id, product_id, quantity, warehouse_id, location_id) VALUES
(1, 1, 50, 1, 1),
(2, 2, 40, 1, 1),
(3, 3, 30, 1, 1);

-- Demo Purchases
INSERT INTO purchases (purchase_id, supplier_id, total_amount, purchase_date) VALUES
(1, 1, 100.00, CURDATE()),
(2, 1, 150.00, CURDATE()-INTERVAL 2 DAY);
