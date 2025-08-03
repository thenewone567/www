-- Clear and Insert Test Data for Hardware Store Management System
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
(5, 'Stanley', 1),
(6, 'Milwaukee', 1),
(7, 'Black & Decker', 1),
(8, 'Ryobi', 1);

-- Insert Units
INSERT INTO `units` (`unit_id`, `unit_name`, `is_active`) VALUES
(1, 'Piece', 1),
(2, 'Box', 1),
(3, 'Pack', 1),
(4, 'Set', 1),
(5, 'Meter', 1),
(6, 'Kilogram', 1),
(7, 'Liter', 1),
(8, 'Dozen', 1);

-- Insert Suppliers
INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_info`, `gst_info`, `due_amount`) VALUES
(1, 'Tool World Distributors', '555-0123, toolworld@email.com', 'GST123456789', 0.00),
(2, 'Hardware Supply Co.', '555-0456, hardware@supply.com', 'GST987654321', 0.00),
(3, 'Electric Components Ltd.', '555-0789, electric@components.com', 'GST456789123', 0.00),
(4, 'Safety First Equipment', '555-0321, safety@first.com', 'GST789123456', 0.00),
(5, 'Paint & More Supplies', '555-0654, paint@more.com', 'GST321654987', 0.00);

-- Insert Customers
INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_info`, `credit_limit`) VALUES
(1, 'Construction Plus LLC', '555-1001, contact@constructionplus.com', 5000.00),
(2, 'Home Renovation Co.', '555-1002, info@homereno.com', 3000.00),
(3, 'DIY Enthusiast Store', '555-1003, sales@diystore.com', 2000.00),
(4, 'Professional Contractors', '555-1004, pro@contractors.com', 7500.00),
(5, 'Small Projects Inc.', '555-1005, small@projects.com', 1500.00),
(6, 'John Smith', '555-1006, john.smith@email.com', 500.00),
(7, 'Jane Doe', '555-1007, jane.doe@email.com', 300.00),
(8, 'Mike Johnson', '555-1008, mike.j@email.com', 800.00);

-- Insert Products
INSERT INTO `products` (`product_id`, `product_name`, `sku`, `category_id`, `brand_id`, `unit_id`, `min_stock_level`, `max_stock_level`, `reorder_level`, `is_active`) VALUES
(1, 'Cordless Drill 18V', 'DW-CD18V-001', 1, 1, 1, 5, 50, 10, 1),
(2, 'Circular Saw 7-1/4"', 'MK-CS7-002', 1, 2, 1, 3, 30, 8, 1),
(3, 'Hammer Set 3-Piece', 'CR-HS3-003', 2, 4, 4, 10, 100, 20, 1),
(4, 'Screwdriver Set 20-Piece', 'ST-SD20-004', 2, 5, 4, 15, 80, 25, 1),
(5, 'Socket Wrench Set', 'ML-SWS-005', 2, 6, 4, 8, 60, 15, 1),
(6, 'Hex Bolts M8x50 (Pack of 100)', 'HW-HB850-006', 3, 1, 3, 50, 500, 100, 1),
(7, 'Wood Screws 2" (Box of 500)', 'HW-WS2-007', 3, 1, 2, 20, 200, 40, 1),
(8, 'Extension Cord 25ft', 'EL-EC25-008', 4, 7, 1, 12, 80, 20, 1),
(9, 'Wire Nuts Assorted', 'EL-WNA-009', 4, 1, 3, 30, 300, 60, 1),
(10, 'PVC Pipe 1/2" x 10ft', 'PL-PVC12-010', 5, 1, 1, 25, 200, 50, 1),
(11, 'Pipe Fitting Elbow 1/2"', 'PL-PFE12-011', 5, 1, 1, 40, 400, 80, 1),
(12, 'Safety Glasses Clear', 'SF-SGC-012', 6, 4, 1, 20, 150, 35, 1),
(13, 'Work Gloves Large', 'SF-WGL-013', 6, 1, 3, 25, 200, 50, 1),
(14, 'Interior Paint White 1 Gallon', 'PT-IPW1-014', 7, 1, 1, 8, 60, 15, 1),
(15, 'Paint Brush Set 4-Piece', 'PT-PBS4-015', 7, 1, 4, 12, 80, 20, 1),
(16, 'Garden Hose 50ft', 'GT-GH50-016', 8, 1, 1, 6, 40, 12, 1),
(17, 'Pruning Shears', 'GT-PS-017', 8, 1, 1, 10, 70, 18, 1),
(18, 'LED Work Light', 'EL-LWL-018', 4, 3, 1, 8, 50, 15, 1),
(19, 'Measuring Tape 25ft', 'HT-MT25-019', 2, 5, 1, 15, 100, 25, 1),
(20, 'Level 24"', 'HT-L24-020', 2, 5, 1, 5, 40, 10, 1);

-- Insert Stock data
INSERT INTO `stock` (`stock_id`, `product_id`, `quantity`, `cost_price`, `selling_price`, `last_updated`) VALUES
(1, 1, 35, 85.00, 120.00, NOW()),
(2, 2, 22, 145.00, 199.99, NOW()),
(3, 3, 78, 25.00, 39.99, NOW()),
(4, 4, 65, 18.00, 29.99, NOW()),
(5, 5, 42, 65.00, 89.99, NOW()),
(6, 6, 385, 0.35, 0.75, NOW()),
(7, 7, 156, 12.00, 19.99, NOW()),
(8, 8, 48, 22.00, 34.99, NOW()),
(9, 9, 245, 3.50, 6.99, NOW()),
(10, 10, 132, 8.50, 14.99, NOW()),
(11, 11, 298, 1.25, 2.49, NOW()),
(12, 12, 89, 4.50, 8.99, NOW()),
(13, 13, 156, 6.00, 12.99, NOW()),
(14, 14, 34, 28.00, 45.99, NOW()),
(15, 15, 67, 15.00, 24.99, NOW()),
(16, 16, 28, 32.00, 49.99, NOW()),
(17, 17, 45, 18.00, 29.99, NOW()),
(18, 18, 33, 25.00, 39.99, NOW()),
(19, 19, 72, 12.00, 19.99, NOW()),
(20, 20, 24, 22.00, 34.99, NOW());

-- Insert Sales (last 30 days)
INSERT INTO `sales` (`sale_id`, `customer_id`, `sale_date`, `total_amount`, `payment_mode`) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 1250.75, 'Credit'),
(2, 2, DATE_SUB(NOW(), INTERVAL 2 DAY), 89.99, 'Cash'),
(3, 6, DATE_SUB(NOW(), INTERVAL 3 DAY), 159.98, 'Debit Card'),
(4, 3, DATE_SUB(NOW(), INTERVAL 4 DAY), 245.50, 'Credit'),
(5, 7, DATE_SUB(NOW(), INTERVAL 5 DAY), 45.99, 'Cash'),
(6, 4, DATE_SUB(NOW(), INTERVAL 6 DAY), 2100.25, 'Credit'),
(7, 8, DATE_SUB(NOW(), INTERVAL 7 DAY), 75.48, 'Debit Card'),
(8, 1, DATE_SUB(NOW(), INTERVAL 8 DAY), 345.99, 'Credit'),
(9, 5, DATE_SUB(NOW(), INTERVAL 9 DAY), 129.99, 'Cash'),
(10, 2, DATE_SUB(NOW(), INTERVAL 10 DAY), 89.97, 'Credit'),
(11, 6, DATE_SUB(NOW(), INTERVAL 12 DAY), 199.99, 'Cash'),
(12, 3, DATE_SUB(NOW(), INTERVAL 14 DAY), 456.78, 'Credit'),
(13, 7, DATE_SUB(NOW(), INTERVAL 15 DAY), 67.99, 'Debit Card'),
(14, 4, DATE_SUB(NOW(), INTERVAL 18 DAY), 1899.99, 'Credit'),
(15, 8, DATE_SUB(NOW(), INTERVAL 20 DAY), 234.56, 'Cash'),
(16, 1, DATE_SUB(NOW(), INTERVAL 22 DAY), 567.89, 'Credit'),
(17, 5, DATE_SUB(NOW(), INTERVAL 25 DAY), 123.45, 'Debit Card'),
(18, 2, DATE_SUB(NOW(), INTERVAL 28 DAY), 345.67, 'Cash'),
(19, 6, DATE_SUB(NOW(), INTERVAL 29 DAY), 89.99, 'Credit'),
(20, 3, DATE_SUB(NOW(), INTERVAL 30 DAY), 678.90, 'Credit');

-- Insert Sale Items
INSERT INTO `sale_items` (`sale_item_id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `discount`) VALUES
-- Sale 1 items
(1, 1, 1, 3, 120.00, 0.00),
(2, 1, 2, 2, 199.99, 10.00),
(3, 1, 8, 5, 34.99, 0.00),
(4, 1, 12, 10, 8.99, 0.00),
-- Sale 2 items
(5, 2, 5, 1, 89.99, 0.00),
-- Sale 3 items
(6, 3, 3, 2, 39.99, 0.00),
(7, 3, 4, 2, 29.99, 0.00),
(8, 3, 19, 2, 19.99, 0.00),
-- Sale 4 items
(9, 4, 14, 3, 45.99, 5.00),
(10, 4, 15, 4, 24.99, 0.00),
(11, 4, 18, 2, 39.99, 0.00),
-- Sale 5 items
(12, 5, 14, 1, 45.99, 0.00),
-- Sale 6 items
(13, 6, 1, 5, 120.00, 15.00),
(14, 6, 2, 3, 199.99, 20.00),
(15, 6, 5, 8, 89.99, 10.00),
(16, 6, 20, 5, 34.99, 0.00),
-- Sale 7 items
(17, 7, 12, 3, 8.99, 0.00),
(18, 7, 13, 3, 12.99, 0.00),
(19, 7, 9, 2, 6.99, 0.00),
-- Sale 8 items
(20, 8, 16, 2, 49.99, 5.00),
(21, 8, 17, 3, 29.99, 0.00),
(22, 8, 6, 50, 0.75, 0.00),
-- Additional sale items for remaining sales...
(23, 9, 4, 2, 29.99, 0.00),
(24, 9, 7, 3, 19.99, 0.00),
(25, 9, 11, 20, 2.49, 0.00),
(26, 10, 8, 2, 34.99, 0.00),
(27, 10, 19, 1, 19.99, 0.00),
(28, 11, 2, 1, 199.99, 0.00),
(29, 12, 1, 2, 120.00, 5.00),
(30, 12, 3, 3, 39.99, 0.00),
(31, 12, 15, 4, 24.99, 0.00),
(32, 12, 18, 3, 39.99, 0.00),
(33, 13, 13, 2, 12.99, 0.00),
(34, 13, 12, 5, 8.99, 0.00),
(35, 14, 1, 8, 120.00, 25.00),
(36, 14, 2, 4, 199.99, 30.00),
(37, 14, 5, 3, 89.99, 15.00),
(38, 15, 14, 2, 45.99, 0.00),
(39, 15, 15, 3, 24.99, 0.00),
(40, 15, 16, 2, 49.99, 0.00),
(41, 16, 3, 4, 39.99, 0.00),
(42, 16, 4, 5, 29.99, 0.00),
(43, 16, 8, 3, 34.99, 0.00),
(44, 16, 19, 6, 19.99, 0.00),
(45, 17, 17, 2, 29.99, 0.00),
(46, 17, 20, 2, 34.99, 0.00),
(47, 18, 1, 1, 120.00, 0.00),
(48, 18, 5, 1, 89.99, 0.00),
(49, 18, 18, 3, 39.99, 0.00),
(50, 19, 3, 1, 39.99, 0.00),
(51, 19, 16, 1, 49.99, 0.00),
(52, 20, 2, 2, 199.99, 15.00),
(53, 20, 14, 4, 45.99, 0.00),
(54, 20, 15, 5, 24.99, 0.00);

-- Insert Purchases (last 60 days)
INSERT INTO `purchases` (`purchase_id`, `supplier_id`, `purchase_date`, `total_amount`) VALUES
(1, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 3500.00),
(2, 2, DATE_SUB(NOW(), INTERVAL 12 DAY), 2200.00),
(3, 3, DATE_SUB(NOW(), INTERVAL 18 DAY), 1850.00),
(4, 4, DATE_SUB(NOW(), INTERVAL 25 DAY), 1200.00),
(5, 5, DATE_SUB(NOW(), INTERVAL 35 DAY), 980.00),
(6, 1, DATE_SUB(NOW(), INTERVAL 45 DAY), 4200.00),
(7, 2, DATE_SUB(NOW(), INTERVAL 55 DAY), 2800.00);

-- Insert Purchase Items
INSERT INTO `purchase_items` (`purchase_item_id`, `purchase_id`, `product_id`, `quantity`, `unit_cost`) VALUES
-- Purchase 1 items
(1, 1, 1, 20, 85.00),
(2, 1, 2, 15, 145.00),
(3, 1, 5, 10, 65.00),
-- Purchase 2 items
(4, 2, 3, 50, 25.00),
(5, 2, 4, 30, 18.00),
(6, 2, 19, 40, 12.00),
(7, 2, 20, 25, 22.00),
-- Purchase 3 items
(8, 3, 8, 30, 22.00),
(9, 3, 9, 100, 3.50),
(10, 3, 18, 25, 25.00),
-- Purchase 4 items
(11, 4, 12, 60, 4.50),
(12, 4, 13, 80, 6.00),
-- Purchase 5 items
(13, 5, 14, 20, 28.00),
(14, 5, 15, 30, 15.00),
-- Purchase 6 items
(15, 6, 1, 25, 85.00),
(16, 6, 2, 20, 145.00),
(17, 6, 16, 15, 32.00),
(18, 6, 17, 25, 18.00),
-- Purchase 7 items
(19, 7, 6, 200, 0.35),
(20, 7, 7, 100, 12.00),
(21, 7, 10, 80, 8.50),
(22, 7, 11, 150, 1.25);

-- Insert Activity Logs
INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `target_type`, `target_id`, `log_timestamp`) VALUES
(1, 1, 'Sale Completed', 'sale', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 1, 'Product Added', 'product', 20, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 1, 'Low Stock Alert', 'product', 6, DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(4, 1, 'New Customer', 'customer', 8, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(5, 1, 'Purchase Order Created', 'purchase', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 1, 'Stock Updated', 'product', 15, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(7, 1, 'Sale Completed', 'sale', 3, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(8, 1, 'Product Updated', 'product', 12, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Insert some notifications
INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Low stock alert: Hex Bolts M8x50 (6 remaining)', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(2, 1, 'New customer registered: Mike Johnson', 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 1, 'Monthly sales target reached!', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(4, 1, 'Supplier payment due: Tool World Distributors', 0, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Display summary
SELECT 'Test data inserted successfully!' AS Status;
SELECT 
    (SELECT COUNT(*) FROM products) AS Products,
    (SELECT COUNT(*) FROM customers) AS Customers,
    (SELECT COUNT(*) FROM sales) AS Sales,
    (SELECT COUNT(*) FROM purchases) AS Purchases,
    (SELECT COUNT(*) FROM activity_logs) AS Activities,
    (SELECT COUNT(*) FROM notifications) AS Notifications;
