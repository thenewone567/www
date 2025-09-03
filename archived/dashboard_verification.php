<?php
require_once 'bootstrap.php';

// Simulate being logged in
if (!isset($_SESSION)) {
    session_start();
}
$_SESSION['user_id'] = 1;

// Load the actual dashboard controller to get the real data array
require_once 'app/controllers/DashboardController.php';

// Create an instance but capture the data manually  
$controller = new DashboardController();
$dashboard = $controller->dashboardModel;

// Get the period
$period = isset($_GET['period']) ? (int) $_GET['period'] : 30;
if (!in_array($period, [7, 30, 90])) {
    $period = 30;
}

// Get the data exactly like the controller does
$inventoryStatusDistribution = $dashboard->getInventoryStatusDistribution();
$priceRangeDistribution = $dashboard->getPriceRangeDistribution();
$salesByCategory = $dashboard->getSalesByCategory($period);
$lowInventory = $dashboard->getLowInventoryProducts(10);

// Build the data array like the controller
$data = [
    'inventory_status_distribution' => $inventoryStatusDistribution,
    'price_range_distribution' => $priceRangeDistribution,
    'sales_by_category' => $salesByCategory,
    'low_inventory' => $lowInventory,
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Data Verification</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container { width: 300px; height: 200px; margin: 20px; display: inline-block; }
        .debug-info { background: #f5f5f5; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Dashboard Data Verification</h1>
    
    <div class="debug-info">
        <h3>Raw Data from Controller:</h3>
        <p><strong>Inventory Status Distribution:</strong> <?php echo json_encode($data['inventory_status_distribution'] ?? [0, 0, 0, 0]); ?></p>
        <p><strong>Price Range Distribution:</strong> <?php echo json_encode($data['price_range_distribution'] ?? [0, 0, 0, 0, 0]); ?></p>
        <p><strong>Sales by Category Count:</strong> <?php echo count($data['sales_by_category'] ?? []); ?></p>
        <p><strong>Low Inventory Count:</strong> <?php echo count($data['low_inventory'] ?? []); ?></p>
    </div>

    <div class="chart-container">
        <h3>Inventory Status</h3>
        <canvas id="inventoryChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Price Range</h3>
        <canvas id="priceChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Category Performance</h3>
        <canvas id="categoryChart"></canvas>
    </div>

    <div id="error-log" class="debug-info">
        <h3>Error Log:</h3>
        <div id="errors"></div>
    </div>

    <script>
        // Capture any errors
        window.onerror = function(msg, url, line, col, error) {
            document.getElementById('errors').innerHTML += '<p>Error: ' + msg + ' at line ' + line + '</p>';
            return false;
        };

        console.log('Starting dashboard verification...');

        try {
            // Inventory Status Chart
            const inventoryCtx = document.getElementById('inventoryChart');
            console.log('inventoryChart element:', inventoryCtx);

            const invDist = <?php echo json_encode($data['inventory_status_distribution'] ?? [0, 0, 0, 0]); ?>;
            console.log('Inventory data:', invDist);

            if (inventoryCtx) {
                const inventoryChart = new Chart(inventoryCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Reorder Level'],
                        datasets: [{
                            data: invDist,
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                        }]
                    }
                });
                console.log('Inventory chart created successfully');
            }

            // Price Range Chart
            const priceCtx = document.getElementById('priceChart');
            const priceDist = <?php echo json_encode($data['price_range_distribution'] ?? [0, 0, 0, 0, 0]); ?>;
            console.log('Price data:', priceDist);

            if (priceCtx) {
                const priceChart = new Chart(priceCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['₹0-500', '₹500-2K', '₹2K-5K', '₹5K-10K', '₹10K+'],
                        datasets: [{
                            label: 'Number of Products',
                            data: priceDist,
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                console.log('Price chart created successfully');
            }

            // Category Performance Chart
            const categoryCtx = document.getElementById('categoryChart');
            const categoryLabels = <?php echo json_encode(array_column($data['sales_by_category'] ?? [], 'category_name')); ?> || ['Power Tools', 'Hand Tools', 'Hardware', 'Electrical', 'Plumbing', 'Safety'];
            const categoryValues = <?php echo json_encode(array_column($data['low_inventory'] ?? [], 'current_inventory')); ?> || [45, 38, 32, 28, 22, 15];
            console.log('Category data - labels:', categoryLabels, 'values:', categoryValues);

            if (categoryCtx) {
                const categoryChart = new Chart(categoryCtx.getContext('2d'), {
                    type: 'bar',
                    options: { indexAxis: 'y' },
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            label: 'Inventory Count',
                            data: categoryValues,
                            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c']
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                console.log('Category chart created successfully');
            }

            console.log('All charts initialized successfully!');
            document.getElementById('errors').innerHTML += '<p style="color: green;">✅ All charts created successfully!</p>';

        } catch (error) {
            console.error('Error creating charts:', error);
            document.getElementById('errors').innerHTML += '<p style="color: red;">❌ Error: ' + error.message + '</p>';
        }
    </script>
</body>
</html>
