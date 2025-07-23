document.addEventListener("DOMContentLoaded", function() {
    // Sidebar toggle
    const menuToggle = document.getElementById("menu-toggle");
    const wrapper = document.getElementById("wrapper");

    if (menuToggle) {
        menuToggle.addEventListener("click", function(e) {
            e.preventDefault();
            wrapper.classList.toggle("toggled");
        });
    }

    // Inventory Chart (Doughnut)
    const inventoryChartCtx = document.getElementById('inventoryChart').getContext('2d');
    const inventoryChart = new Chart(inventoryChartCtx, {
        type: 'doughnut',
        data: {
            labels: ['Receiving', 'Shop', 'Warehouse'],
            datasets: [{
                label: 'Inventory',
                data: [10, 60, 30],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Financials Chart (Bar)
    const financialsChartCtx = document.getElementById('financialsChart').getContext('2d');
    const financialsChart = new Chart(financialsChartCtx, {
        type: 'bar',
        data: {
            labels: ['Sale', 'Purchase', 'GST', 'Expenses', 'Profit'],
            datasets: [{
                label: 'Amount in $',
                data: [12000, 8000, 2000, 1500, 4500],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
