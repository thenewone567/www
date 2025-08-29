<?php
require_once __DIR__ . '/../bootstrap.php';

$cases = [
    ['user_id' => 6, 'source_table' => 'users'],
    ['user_id' => 3, 'source_table' => 'users'],
    ['user_id' => 5, 'source_table' => 'customers']
];

foreach ($cases as $case) {
    echo "\n--- Testing toggle for user_id={$case['user_id']} source_table={$case['source_table']} ---\n";

    // prepare env
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_SESSION['user_id'] = 1; // admin

    $model = new User();

    // snapshot before
    $before = $model->getUserByIdAndTable($case['user_id'], $case['source_table']);
    $beforeStatus = null;
    if ($before) {
        $beforeStatus = $before->status ?? ($before->is_active ?? null);
    }
    echo "Before status raw: " . var_export($beforeStatus, true) . "\n";

    // determine new status
    $newStatus = ('' . $beforeStatus) === '1' || strtolower('' . $beforeStatus) === 'active' ? 'inactive' : 'active';

    // set POST
    $_POST = [];
    $_POST['user_id'] = $case['user_id'];
    $_POST['source_table'] = $case['source_table'];
    $_POST['status'] = $newStatus;
    // include composite id for new API
    $_POST['composite_id'] = $case['source_table'] . ':' . $case['user_id'];

    // call controller
    $ctrl = new AdminController();
    ob_start();
    $ctrl->toggleUserStatus();
    $resp = ob_get_clean();
    echo "Controller response: $resp\n";

    // snapshot after
    $after = $model->getUserByIdAndTable($case['user_id'], $case['source_table']);
    $afterStatus = null;
    if ($after) {
        $afterStatus = $after->status ?? ($after->is_active ?? null);
    }
    echo "After status raw: " . var_export($afterStatus, true) . "\n";

    // Also try resolving cross-table if direct table didn't find
    if (!$after) {
        $resolved = $model->getUserById($case['user_id']);
        echo "Resolved source_table: " . var_export($resolved->source_table ?? null, true) . "\n";
        $afterResolved = $model->getUserByIdAndTable($case['user_id'], $resolved->source_table ?? '');
        echo "After resolved status raw: " . var_export($afterResolved ? ($afterResolved->status ?? ($afterResolved->is_active ?? null)) : null, true) . "\n";
    }

    // show recent log lines
    $logPath = __DIR__ . '/../storage/logs/app.log';
    if (file_exists($logPath)) {
        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $tail = array_slice($lines, -30);
        echo "--- Log tail ---\n";
        foreach ($tail as $l)
            echo $l . "\n";
    }
}

?>