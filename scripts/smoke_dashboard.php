<?php
require __DIR__ . '/../bootstrap.php';
// Simple CLI smoke test to exercise the dashboard index path
$start = microtime(true);
try {
    $c = new DashboardController();
    // Suppress direct output buffering in CLI to measure server-side time only
    ob_start();
    $c->index();
    ob_end_clean();
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
}
$elapsed = microtime(true) - $start;
echo PHP_EOL . 'Elapsed: ' . round($elapsed, 2) . "s\n";
