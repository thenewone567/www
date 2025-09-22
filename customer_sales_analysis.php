<?php
require_once 'bootstrap.php';

try {
    $db = new Database();
    
    echo "<h2>Customer Sales Issue Investigation</h2>";
    echo "<p>Investigating why Construction Plus LLC has excessive sales that should be walk-in customers</p>";
    
    // Check all customers to see what we have
    echo "<h3>Current Customer Database</h3>";
    $db->query("SELECT * FROM customers ORDER BY customer_id");
    $customers = $db->resultSet();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Name</th><th>Contact Info Preview</th><th>Credit Limit</th><th>Status</th>";
    echo "</tr>";
    
    foreach ($customers as $customer) {
        $contactInfo = json_decode($customer->contact_info, true);
        $contactPreview = isset($contactInfo['contact_person']) ? $contactInfo['contact_person'] : 'N/A';
        
        $bgColor = ($customer->customer_id == 1) ? 'background-color: #ffeeee;' : '';
        echo "<tr style='$bgColor'>";
        echo "<td>{$customer->customer_id}</td>";
        echo "<td>{$customer->customer_name}</td>";
        echo "<td>{$contactPreview}</td>";
        echo "<td>$" . number_format($customer->credit_limit, 2) . "</td>";
        echo "<td>{$customer->status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check sales distribution
    echo "<h3>Sales Distribution by Customer</h3>";
    $db->query("SELECT 
                   s.customer_id,
                   c.customer_name,
                   COUNT(*) as sales_count,
                   SUM(s.total_amount) as total_amount,
                   AVG(s.total_amount) as avg_amount,
                   MIN(s.sale_date) as first_sale,
                   MAX(s.sale_date) as last_sale
                FROM sales s 
                LEFT JOIN customers c ON s.customer_id = c.customer_id 
                GROUP BY s.customer_id, c.customer_name 
                ORDER BY total_amount DESC");
    $distribution = $db->resultSet();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>Customer ID</th><th>Customer Name</th><th>Sales Count</th><th>Total Amount</th><th>Avg Amount</th><th>First Sale</th><th>Last Sale</th>";
    echo "</tr>";
    
    foreach ($distribution as $dist) {
        $bgColor = ($dist->customer_id == 1) ? 'background-color: #ffeeee;' : '';
        echo "<tr style='$bgColor'>";
        echo "<td>{$dist->customer_id}</td>";
        echo "<td>" . ($dist->customer_name ?: 'Unknown/Deleted') . "</td>";
        echo "<td>{$dist->sales_count}</td>";
        echo "<td>$" . number_format($dist->total_amount, 2) . "</td>";
        echo "<td>$" . number_format($dist->avg_amount, 2) . "</td>";
        echo "<td>{$dist->first_sale}</td>";
        echo "<td>{$dist->last_sale}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check payment methods for Construction Plus LLC
    echo "<h3>Payment Methods for Construction Plus LLC (Customer ID: 1)</h3>";
    $db->query("SELECT 
                   payment_mode,
                   COUNT(*) as count,
                   SUM(total_amount) as total_amount
                FROM sales 
                WHERE customer_id = 1 
                GROUP BY payment_mode 
                ORDER BY total_amount DESC");
    $paymentMethods = $db->resultSet();
    
    if ($paymentMethods) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Payment Method</th><th>Count</th><th>Total Amount</th>";
        echo "</tr>";
        
        foreach ($paymentMethods as $method) {
            echo "<tr>";
            echo "<td>{$method->payment_mode}</td>";
            echo "<td>{$method->count}</td>";
            echo "<td>$" . number_format($method->total_amount, 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check for null/empty customer_id sales
    echo "<h3>Sales with NULL or Empty Customer ID</h3>";
    $db->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM sales WHERE customer_id IS NULL OR customer_id = 0");
    $nullSales = $db->single();
    
    echo "<p>Sales with NULL customer_id: {$nullSales->count} sales, Total: $" . number_format($nullSales->total ?? 0, 2) . "</p>";
    
    // Sample recent sales for Construction Plus LLC
    echo "<h3>Recent Sales for Construction Plus LLC (Last 10)</h3>";
    $db->query("SELECT * FROM sales WHERE customer_id = 1 ORDER BY sale_date DESC LIMIT 10");
    $recentSales = $db->resultSet();
    
    if ($recentSales) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Sale ID</th><th>Date</th><th>Amount</th><th>Payment Mode</th>";
        echo "</tr>";
        
        foreach ($recentSales as $sale) {
            echo "<tr>";
            echo "<td>{$sale->sale_id}</td>";
            echo "<td>{$sale->sale_date}</td>";
            echo "<td>$" . number_format($sale->total_amount, 2) . "</td>";
            echo "<td>{$sale->payment_mode}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>Analysis & Recommendations</h3>";
    echo "<div style='background-color: #ffffcc; padding: 10px; border: 1px solid #cccc00;'>";
    echo "<p><strong>Issue Analysis:</strong></p>";
    echo "<ul>";
    echo "<li>Construction Plus LLC (Customer ID: 1) appears to have excessive sales volume</li>";
    echo "<li>This suggests that cash/walk-in sales might be incorrectly assigned to this customer</li>";
    echo "<li>There should be a dedicated 'Walk-in Customer' or 'Cash Customer' entry for unregistered sales</li>";
    echo "</ul>";
    echo "<p><strong>Recommended Solution:</strong></p>";
    echo "<ol>";
    echo "<li>Create a dedicated 'Walk-in Customer' entry in the customers table</li>";
    echo "<li>Identify cash/retail sales currently assigned to Construction Plus LLC</li>";
    echo "<li>Reassign these sales to the walk-in customer ID</li>";
    echo "<li>Update POS system to use walk-in customer for cash sales by default</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
