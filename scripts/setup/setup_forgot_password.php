<?php
/**
 * Setup script to create password_reset_tokens table
 * Run this file once to add the forgot password functionality
 */

require_once '../../bootstrap.php';

try {
    $db = new Database();

    echo "Creating password_reset_tokens table...\n";

    // Create the table
    $sql = "
    CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `token` varchar(64) NOT NULL,
      `expires_at` datetime NOT NULL,
      `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `token` (`token`),
      KEY `user_id` (`user_id`),
      KEY `expires_at` (`expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->query($sql);
    if ($db->execute()) {
        echo "✅ password_reset_tokens table created successfully!\n";
    } else {
        echo "❌ Failed to create password_reset_tokens table\n";
    }

    // Create index for faster lookups
    $indexSql = "CREATE INDEX IF NOT EXISTS idx_token_expires ON password_reset_tokens(token, expires_at)";
    $db->query($indexSql);
    if ($db->execute()) {
        echo "✅ Index created successfully!\n";
    } else {
        echo "❌ Failed to create index\n";
    }

    echo "\n🎉 Forgot password functionality is now ready!\n";
    echo "You can now use the 'Forgot your password?' link on the login page.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>