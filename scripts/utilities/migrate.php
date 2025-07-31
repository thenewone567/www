<?php
/**
 * Database Migration Runner
 */

require_once __DIR__ . "/../bootstrap.php";

class MigrationRunner {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_migration (migration)
        )";
        
        $this->db->query($sql);
        $this->db->execute();
    }
    
    public function runMigrations() {
        $migrationsDir = __DIR__ . "/../database/migrations/";
        $files = glob($migrationsDir . "*.sql");
        sort($files);
        
        echo "<h3>Running Database Migrations</h3>";
        
        foreach ($files as $file) {
            $migrationName = basename($file);
            
            // Check if already executed
            $this->db->query("SELECT id FROM migrations WHERE migration = :migration");
            $this->db->bind(":migration", $migrationName);
            
            if (!$this->db->single()) {
                echo "Executing: $migrationName<br>";
                
                $sql = file_get_contents($file);
                $statements = array_filter(array_map("trim", explode(";", $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        try {
                            $this->db->query($statement);
                            $this->db->execute();
                        } catch (Exception $e) {
                            echo "Error in $migrationName: " . $e->getMessage() . "<br>";
                        }
                    }
                }
                
                // Mark as executed
                $this->db->query("INSERT INTO migrations (migration) VALUES (:migration)");
                $this->db->bind(":migration", $migrationName);
                $this->db->execute();
                
                echo "✅ Completed: $migrationName<br>";
            } else {
                echo "⏭️ Skipped (already executed): $migrationName<br>";
            }
        }
    }
}

// Run if called directly
if (basename(__FILE__) === basename($_SERVER["PHP_SELF"])) {
    echo "<h1>Database Migration Runner</h1>";
    $runner = new MigrationRunner();
    $runner->runMigrations();
    echo "<br><a href=\"../\">← Back to Application</a>";
}
