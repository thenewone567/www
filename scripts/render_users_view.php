<?php
require_once __DIR__ . '/../bootstrap.php';

// Simulate a logged-in admin
$_SESSION['user_id'] = 1;

$ctrl = new AdminController();
ob_start();
$ctrl->users();
$page = ob_get_clean();

// Print a small snippet of the users table rows for inspection
if (preg_match('/<tbody>(.*?)<\/tbody>/is', $page, $m)) {
    $tbody = $m[1];
    // print first 800 chars for brevity
    echo substr(strip_tags($tbody), 0, 2000);
} else {
    echo "Could not extract tbody\n";
}

?>