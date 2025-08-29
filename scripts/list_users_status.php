<?php
require_once __DIR__ . '/../bootstrap.php';

$model = new User();
$users = $model->getAllUsersWithCategories();

$rows = [];
foreach ($users as $u) {
    $rawStatus = $u->status ?? ($u->is_active ?? ($u->customer_status ?? null));
    $isActive = ($rawStatus === 'active' || $rawStatus == 1 || $rawStatus === true);
    $rows[] = [
        'user_id'      => $u->user_id,
        'source_table' => $u->source_table ?? 'users',
        'name'         => $u->name ?? ($u->username ?? ''),
        'rawStatus'    => $rawStatus,
        'isActive'     => $isActive
    ];
}

foreach ($rows as $r) {
    echo implode(' | ', [$r['user_id'], $r['source_table'], $r['name'], var_export($r['rawStatus'], true), $r['isActive'] ? 'ACTIVE' : 'INACTIVE']) . "\n";
}
?>