<?php
// DB Params
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'master_hardware');

// App Root
define('APPROOT', realpath(__DIR__ . '/../'));
define('DS', DIRECTORY_SEPARATOR);
// URL Root
define('URLROOT', 'http://localhost');
// Site Name
define('SITENAME', 'Bhai Ji Traders');
// App Version
define('APPVERSION', '1.0.0');

// Load Database class
require_once __DIR__ . '/Database.php';

// Mobile API Security
define('JWT_SECRET', 'your-super-secret-jwt-key-change-this-in-production-2024-hardware-store');
