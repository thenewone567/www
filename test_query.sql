SELECT
    p.product_id,
    p.product_name,
    p.sku,
    p.model_number,
    p.image_path,
    p.description,
    p.min_inventory_level,
    p.reorder_level,
    c.category_name,
    b.brand_name,
    u.unit_name,
    ps.supplier_id,
    ps.purchase_price AS supplier_price,
    ps.lead_time_days,
    ps.min_order_quantity,
    ps.is_primary,
    ps.supplier_sku AS supplier_product_code,
    ps.is_active as status,
    COALESCE((SELECT SUM(inv.quantity) FROM inventory inv WHERE inv.product_id = p.product_id), 0) AS current_inventory
FROM products p
JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN brands b ON p.brand_id = b.brand_id
LEFT JOIN units u ON p.unit_id = u.unit_id
WHERE p.is_active = 1 AND ps.supplier_id = 1
ORDER BY p.product_name ASC, ps.purchase_price ASC
LIMIT 5;
