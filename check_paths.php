<?php
define('APPROOT', 'C:\wamp64\www');
define('DS', DIRECTORY_SEPARATOR);

$files = [
    'header' => APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php',
    'list' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'list.php',
    'today' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'today.php',
    'details' => APPROOT . DS . 'app' . DS . 'views' . DS . 'sales' . DS . 'details.php'
];

echo "Checking Sales History View Files:\n";
echo "=================================\n";

foreach ($files as $name => $path) {
    echo sprintf(
        "%-10s: %s - %s\n",
        $name,
        $path,
        file_exists($path) ? 'EXISTS' : 'NOT FOUND'
    );
}
?>