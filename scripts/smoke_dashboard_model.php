<?php
require __DIR__ . '/../bootstrap.php';
$start = microtime(true);
$dashboard = new Dashboard();
try {
    $n = $dashboard->getNewCustomers(30);
    $elapsed = microtime(true) - $start;
    echo "getNewCustomers returned: " . $n . PHP_EOL;
    echo "Elapsed: " . round($elapsed, 2) . "s\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
}
