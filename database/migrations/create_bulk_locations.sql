-- Create bulk locations for receiving shipments
-- Bulk locations start with 'B' prefix for easy identification

INSERT INTO `warehouse_locations` (`location_id`, `location_name`, `rack`, `shelf`) VALUES
(1, 'B-001', 'Bulk Area 1', 'Receiving'),
(2, 'B-002', 'Bulk Area 2', 'Receiving'),
(3, 'B-003', 'Bulk Area 3', 'Receiving'),
(4, 'B-004', 'Bulk Area 4', 'Receiving'),
(5, 'B-005', 'Bulk Area 5', 'Receiving'),
(10, 'A-001', 'Aisle A', 'Shelf 1'),
(11, 'A-002', 'Aisle A', 'Shelf 2'),
(12, 'A-003', 'Aisle A', 'Shelf 3'),
(20, 'B-101', 'Aisle B', 'Shelf 1'),
(21, 'B-102', 'Aisle B', 'Shelf 2'),
(22, 'B-103', 'Aisle B', 'Shelf 3'),
(30, 'C-001', 'Aisle C', 'Shelf 1'),
(31, 'C-002', 'Aisle C', 'Shelf 2'),
(32, 'C-003', 'Aisle C', 'Shelf 3');

-- Auto increment starting from 100 to avoid conflicts
ALTER TABLE `warehouse_locations` AUTO_INCREMENT = 100;
