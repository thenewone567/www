<?php
// Minimal smoke test: load DB config, Database class and Dashboard model only
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../app/Database.php';
require __DIR__ . '/../app/models/Dashboard.php';

$start = microtime(true);
try {
    $dashboard = new Dashboard();
    $n = $dashboard->getNewCustomers(30);
    $elapsed = microtime(true) - $start;
    echo "getNewCustomers returned: " . $n . PHP_EOL;
    echo "Elapsed: " . round($elapsed, 2) . "s\n";
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
}
