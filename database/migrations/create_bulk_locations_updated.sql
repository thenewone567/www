-- Create bulk locations for receiving shipments
-- Bulk locations start with 'B-' prefix for easy identification
-- Using IDs starting from 50 to avoid conflicts

INSERT INTO `warehouse_locations` (`location_id`, `location_name`, `rack`, `shelf`) VALUES
(50, 'B-001', 'Bulk Area 1', 'Receiving'),
(51, 'B-002', 'Bulk Area 2', 'Receiving'),
(52, 'B-003', 'Bulk Area 3', 'Receiving'),
(53, 'B-004', 'Bulk Area 4', 'Receiving'),
(54, 'B-005', 'Bulk Area 5', 'Receiving'),
(55, 'B-006', 'Bulk Area 6', 'Receiving'),
(56, 'B-007', 'Bulk Area 7', 'Receiving'),
(57, 'B-008', 'Bulk Area 8', 'Receiving'),
(58, 'B-009', 'Bulk Area 9', 'Receiving'),
(59, 'B-010', 'Bulk Area 10', 'Receiving');

-- Auto increment starting from 100 to avoid conflicts
ALTER TABLE `warehouse_locations` AUTO_INCREMENT = 100;
