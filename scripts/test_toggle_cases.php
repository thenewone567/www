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

    // determine current status by reading model
    $model = new User();
    $target = $model->getUserByIdAndTable($case['user_id'], $case['source_table']);
    $currentStatus = null;
    if ($target) {
        if (isset($target->status))
            $currentStatus = $target->status;
        elseif (isset($target->is_active))
            $currentStatus = $target->is_active;
        elseif (isset($target->customer_status))
            $currentStatus = $target->customer_status;
    }
    echo "Current status raw: " . var_export($currentStatus, true) . "\n";

    $newStatus = ('' . $currentStatus) === '1' || strtolower('' . $currentStatus) === 'active' ? 'inactive' : 'active';

    // set POST
    $_POST = [];
    $_POST['user_id'] = $case['user_id'];
    $_POST['source_table'] = $case['source_table'];
    $_POST['status'] = $newStatus;

    // call controller
    $ctrl = new AdminController();
    ob_start();
    $ctrl->toggleUserStatus();
    $resp = ob_get_clean();
    echo "Controller response: $resp\n";

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