<h2>Reports & Analytics</h2>

<div class="report-links">
    <ul>
        <li><a href="index.php?page=reports&report=sales">Sales Report</a></li>
        <li><a href="index.php?page=reports&report=purchases">Purchases Report</a></li>
        <li><a href="index.php?page=reports&report=inventory">Inventory Valuation Report</a></li>
    </ul>
</div>

<div class="report-content">
    <?php
    if (isset($_GET['report'])) {
        $report_type = $_GET['report'];
        $report_file = "reports/{$report_type}_report.php";
        if (file_exists($report_file)) {
            include $report_file;
        } else {
            echo "<p>Report not found.</p>";
        }
    }
    ?>
</div>
