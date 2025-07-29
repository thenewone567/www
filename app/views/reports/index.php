<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<h1>Reports</h1>
<p>Select a report to view:</p>
<ul>
    <li><a href="<?php echo URLROOT; ?>/reports/sales">Sales Report</a></li>
    <li><a href="<?php echo URLROOT; ?>/reports/purchases">Purchases Report</a></li>
    <li><a href="<?php echo URLROOT; ?>/reports/salereturns">Sale Returns Report</a></li>
    <li><a href="<?php echo URLROOT; ?>/reports/purchasereturns">Purchase Returns Report</a></li>
</ul>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>