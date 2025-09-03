<?php
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $stmt = $pdo->query('SHOW DATABASES');
    echo "Available databases:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- {$row[0]}\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
