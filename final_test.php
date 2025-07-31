<?php
require 'bootstrap.php';
echo 'Testing final implementation...' . PHP_EOL;
$supplier = new Supplier();
$customer = new Customer(); 
echo 'Suppliers available: ' . count($supplier->getSuppliers()) . PHP_EOL;
echo 'Customers available: ' . count($customer->getCustomers()) . PHP_EOL;
echo 'Implementation complete!' . PHP_EOL;
?>
