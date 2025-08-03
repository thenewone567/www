-- Simple Test Data for Hardware Store Management System
USE master_hardware;

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE `sale_items`;
TRUNCATE TABLE `sales`;
TRUNCATE TABLE `purchase_items`;
TRUNCATE TABLE `purchases`;
TRUNCATE TABLE `stock`;
TRUNCATE TABLE `products`;
TRUNCATE TABLE `activity_logs`;
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `brands`;
TRUNCATE TABLE `units`;
TRUNCATE TABLE `suppliers`;
TRUNCATE TABLE `customers`;

-- Insert Categories
INSERT INTO `categories` (`category_id`, `category_name`, `parent_category_id`, `is_active`) VALUES
(1, 'Power Tools', NULL, 1),
(2, 'Hand Tools', NULL, 1),
(3, 'Hardware', NULL, 1),
(4, 'Electrical', NULL, 1),
(5, 'Plumbing', NULL, 1),
(6, 'Safety Equipment', NULL, 1),
(7, 'Paint & Supplies', NULL, 1),
(8, 'Garden Tools', NULL, 1);

-- Insert Brands
INSERT INTO `brands` (`brand_id`, `brand_name`, `is_active`) VALUES
(1, 'DeWalt', 1),
(2, 'Makita', 1),
(3, 'Bosch', 1),
(4, 'Craftsman', 1),
(5, 'Stanley', 1);

-- Insert Units
INSERT INTO `units` (`unit_id`, `unit_name`, `is_active`) VALUES
(1, 'Piece', 1),
(2, 'Box', 1),
(3, 'Pack', 1),
(4, 'Set', 1),
(5, 'Meter', 1);

-- Insert Suppliers
INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_info`, `gst_info`, `due_amount`) VALUES
(1, 'Tool World Distributors', '555-0123, toolworld@email.com', 'GST123456789', 0.00),
(2, 'Hardware Supply Co.', '555-0456, hardware@supply.com', 'GST987654321', 0.00),
(3, 'Electric Components Ltd.', '555-0789, electric@components.com', 'GST456789123', 0.00);

-- Insert Customers
INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_info`, `credit_limit`) VALUES
(1, 'Construction Plus LLC', '555-1001, contact@constructionplus.com', 5000.00),
(2, 'Home Renovation Co.', '555-1002, info@homereno.com', 3000.00),
(3, 'DIY Enthusiast Store', '555-1003, sales@diystore.com', 2000.00),
(4, 'Professional Contractors', '555-1004, pro@contractors.com', 7500.00),
(5, 'Small Projects Inc.', '555-1005, small@projects.com', 1500.00);

-- Insert Products (simplified)
INSERT INTO `products` (`product_id`, `product_name`, `sku`, `category_id`, `brand_id`, `unit_id`, `min_stock_level`, `max_stock_level`, `reorder_level`, `is_active`) VALUES
(1, 'Cordless Drill 18V', 'DW-CD18V-001', 1, 1, 1, 5, 50, 10, 1),
(2, 'Circular Saw 7-1/4"', 'MK-CS7-002', 1, 2, 1, 3, 30, 8, 1),
(3, 'Hammer Set 3-Piece', 'CR-HS3-003', 2, 4, 4, 10, 100, 20, 1),
(4, 'Screwdriver Set 20-Piece', 'ST-SD20-004', 2, 5, 4, 15, 80, 25, 1),
(5, 'Socket Wrench Set', 'ML-SWS-005', 2, 1, 4, 8, 60, 15, 1),
(6, 'Extension Cord 25ft', 'EL-EC25-008', 4, 3, 1, 12, 80, 20, 1),
(7, 'Safety Glasses Clear', 'SF-SGC-012', 6, 4, 1, 20, 150, 35, 1),
(8, 'Interior Paint White 1 Gallon', 'PT-IPW1-014', 7, 1, 1, 8, 60, 15, 1),
(9, 'Garden Hose 50ft', 'GT-GH50-016', 8, 1, 1, 6, 40, 12, 1),
(10, 'LED Work Light', 'EL-LWL-018', 4, 3, 1, 8, 50, 15, 1);

-- Insert Stock data (simplified based on actual table structure)
INSERT INTO `stock` (`stock_id`, `product_id`, `batch_number`, `quantity`, `location_id`) VALUES
(1, 1, 'BATCH001', 35, NULL),
(2, 2, 'BATCH002', 22, NULL),
(3, 3, 'BATCH003', 78, NULL),
(4, 4, 'BATCH004', 65, NULL),
(5, 5, 'BATCH005', 42, NULL),
(6, 6, 'BATCH006', 48, NULL),
(7, 7, 'BATCH007', 89, NULL),
(8, 8, 'BATCH008', 34, NULL),
(9, 9, 'BATCH009', 28, NULL),
(10, 10, 'BATCH010', 33, NULL);

-- Insert Sales (last 30 days)
INSERT INTO `sales` (`sale_id`, `customer_id`, `sale_date`, `total_amount`, `payment_mode`) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 1250.75, 'Credit'),
(2, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), 189.99, 'Cash'),
(3, 3, DATE_SUB(NOW(), INTERVAL 3 DAY), 359.98, 'Debit Card'),
(4, 4, DATE_SUB(NOW(), INTERVAL 4 DAY), 845.50, 'Credit'),
(5, 5, DATE_SUB(NOW(), INTERVAL 5 DAY), 245.99, 'Cash'),
(6, 1, DATE_SUB(NOW(), INTERVAL 6 DAY), 2100.25, 'Credit'),
(7, 2, DATE_SUB(NOW(), INTERVAL 7 DAY), 175.48, 'Debit Card'),
(8, 3, DATE_SUB(NOW(), INTERVAL 8 DAY), 445.99, 'Credit'),
(9, 4, DATE_SUB(NOW(), INTERVAL 9 DAY), 329.99, 'Cash'),
(10, 5, DATE_SUB(NOW(), INTERVAL 10 DAY), 189.97, 'Credit'),
(11, 1, DATE_SUB(NOW(), INTERVAL 12 DAY), 299.99, 'Cash'),
(12, 2, DATE_SUB(NOW(), INTERVAL 14 DAY), 556.78, 'Credit'),
(13, 3, DATE_SUB(NOW(), INTERVAL 15 DAY), 167.99, 'Debit Card'),
(14, 4, DATE_SUB(NOW(), INTERVAL 18 DAY), 1899.99, 'Credit'),
(15, 5, DATE_SUB(NOW(), INTERVAL 20 DAY), 434.56, 'Cash');

-- Insert Sale Items
INSERT INTO `sale_items` (`sale_item_id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `discount`) VALUES
(1, 1, 1, 3, 120.00, 0.00),
(2, 1, 2, 2, 199.99, 10.00),
(3, 1, 6, 5, 34.99, 0.00),
(4, 2, 5, 1, 89.99, 0.00),
(5, 2, 3, 2, 39.99, 0.00),
(6, 3, 4, 2, 29.99, 0.00),
(7, 3, 10, 2, 39.99, 0.00),
(8, 4, 8, 3, 45.99, 5.00),
(9, 4, 9, 4, 24.99, 0.00),
(10, 4, 7, 2, 39.99, 0.00),
(11, 5, 8, 1, 45.99, 0.00),
(12, 6, 1, 5, 120.00, 15.00),
(13, 6, 2, 3, 199.99, 20.00),
(14, 6, 5, 8, 89.99, 10.00),
(15, 7, 7, 3, 8.99, 0.00),
(16, 7, 3, 3, 12.99, 0.00),
(17, 8, 9, 2, 49.99, 5.00),
(18, 8, 10, 3, 29.99, 0.00),
(19, 9, 4, 2, 29.99, 0.00),
(20, 9, 6, 3, 19.99, 0.00),
(21, 10, 6, 2, 34.99, 0.00),
(22, 11, 2, 1, 199.99, 0.00),
(23, 12, 1, 2, 120.00, 5.00),
(24, 12, 3, 3, 39.99, 0.00),
(25, 13, 7, 2, 12.99, 0.00),
(26, 14, 1, 8, 120.00, 25.00),
(27, 14, 2, 4, 199.99, 30.00),
(28, 15, 8, 2, 45.99, 0.00),
(29, 15, 9, 3, 24.99, 0.00),
(30, 15, 10, 2, 49.99, 0.00);

-- Insert Purchases (last 60 days)
INSERT INTO `purchases` (`purchase_id`, `supplier_id`, `purchase_date`, `total_amount`) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 3500.00),
(2, 2, DATE_SUB(NOW(), INTERVAL 12 DAY), 2200.00),
(3, 3, DATE_SUB(NOW(), INTERVAL 18 DAY), 1850.00),
(4, 1, DATE_SUB(NOW(), INTERVAL 25 DAY), 4200.00),
(5, 2, DATE_SUB(NOW(), INTERVAL 35 DAY), 2800.00);

-- Insert Purchase Items (corrected field name)
INSERT INTO `purchase_items` (`purchase_item_id`, `purchase_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 1, 20, 85.00),
(2, 1, 2, 15, 145.00),
(3, 1, 5, 10, 65.00),
(4, 2, 3, 50, 25.00),
(5, 2, 4, 30, 18.00),
(6, 3, 6, 30, 22.00),
(7, 3, 7, 100, 3.50),
(8, 3, 10, 25, 25.00),
(9, 4, 1, 25, 85.00),
(10, 4, 2, 20, 145.00),
(11, 5, 8, 20, 28.00),
(12, 5, 9, 30, 15.00);

-- Insert Activity Logs
INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `target_type`, `target_id`, `log_timestamp`) VALUES
(1, 1, 'Sale Completed', 'sale', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 1, 'Product Added', 'product', 10, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 1, 'Low Stock Alert', 'product', 6, DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(4, 1, 'New Customer', 'customer', 5, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 1, 'Purchase Order Created', 'purchase', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 1, 'Stock Updated', 'product', 8, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(7, 1, 'Sale Completed', 'sale', 3, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(8, 1, 'Product Updated', 'product', 7, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Insert some notifications
INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Low stock alert: Extension Cord 25ft (48 remaining)', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 1, 'New customer registered: Small Projects Inc.', 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 1, 'Monthly sales target reached!', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 1, 'Supplier payment due: Tool World Distributors', 0, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Display summary
SELECT 'Simple test data inserted successfully!' AS Status;
SELECT 
    (SELECT COUNT(*) FROM products) AS Products,
    (SELECT COUNT(*) FROM customers) AS Customers,
    (SELECT COUNT(*) FROM sales) AS Sales,
    (SELECT COUNT(*) FROM purchases) AS Purchases,
    (SELECT COUNT(*) FROM activity_logs) AS Activities,
    (SELECT COUNT(*) FROM notifications) AS Notifications;
