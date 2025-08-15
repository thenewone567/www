<?php
/*
 * PDO Database Class
 * Connect to database
 * Create prepared statements
 * Bind values
 * Return rows and results
 */
class Database
{
  // Get last error message
  public function getLastError()
  {
    return $this->error;
  }
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $dbname = DB_NAME;

  private $dbh;
  private $stmt;
  private $error;

  public function getDbh()
  {
    return $this->dbh;
  }

  public function __construct()
  {
    // Validate configuration constants
    if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
      error_log('Database configuration constants not defined');
      die('Database configuration error. Please check your configuration files.');
    }

    // Set DSN with additional options
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
    $options = array(
      PDO::ATTR_PERSISTENT => false, // Changed to false for better error handling
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    );

    // Add MySQL-specific option only if available
    if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
      $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4";
    }

    // Create PDO instance with detailed error reporting
    try {
      error_log("Attempting database connection to: " . $this->host . "/" . $this->dbname);
      $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
      error_log("Database connection successful");
    } catch (PDOException $e) {
      $this->error = $e->getMessage();

      // Log detailed error for debugging
      error_log('Database Connection Error Details: ' . $this->error);
      error_log('Host: ' . $this->host);
      error_log('Database: ' . $this->dbname);
      error_log('User: ' . $this->user);

      // Check specific error types and provide helpful messages
      if (strpos($this->error, 'Access denied') !== false) {
        die('Database access denied. Please check your username and password in config/database.php');
      } elseif (strpos($this->error, 'Unknown database') !== false) {
        die('Database "' . $this->dbname . '" not found. Please create the database or check the name in config/database.php');
      } elseif (strpos($this->error, 'Connection refused') !== false) {
        die('Cannot connect to MySQL server. Please ensure WAMP/MySQL is running and check the host in config/database.php');
      } else {
        die('Database connection failed: ' . $this->error . '. Please check your configuration or contact an administrator.');
      }
    }
  }

  // Prepare statement with query
  public function query($sql)
  {
    // Check if connection was established before preparing
    if ($this->dbh === null) {
      error_log('Cannot prepare query. Database connection not established.');
      return false;
    }
    $this->stmt = $this->dbh->prepare($sql);
    return true;
  }

  // Bind values
  public function bind($param, $value, $type = null)
  {
    if ($this->stmt === null) {
      error_log('Cannot bind values. No prepared statement exists.');
      return false;
    }

    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
      }
    }

    $this->stmt->bindValue($param, $value, $type);
    return true;
  }

  // Execute the prepared statement
  public function execute()
  {
    if ($this->stmt === null) {
      error_log('Cannot execute. No prepared statement exists.');
      return false;
    }

    try {
      return $this->stmt->execute();
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      error_log('SQL Execute Error: ' . $this->error);
      return false;
    }
  }

  // Get result set as array of objects
  public function resultSet()
  {
    if ($this->stmt === null) {
      error_log('Cannot get result set. No prepared statement exists.');
      return [];
    }

    try {
      return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      error_log('SQL Fetch Error in resultSet(): ' . $e->getMessage());
      return [];
    }
  }

  // Get single record as object
  public function single()
  {
    if ($this->stmt === null) {
      error_log('Cannot get single result. No prepared statement exists.');
      return null;
    }

    try {
      return $this->stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      error_log('SQL Fetch Error in single(): ' . $e->getMessage());
      return null;
    }
  }

  // Execute prepared statement and get single record
  public function executeSingle()
  {
    if ($this->stmt === null) {
      error_log('Cannot get single result. No prepared statement exists.');
      return null;
    }

    if ($this->execute()) {
      return $this->stmt->fetch(PDO::FETCH_OBJ);
    }
    return null;
  }

  // Execute prepared statement and get result set
  public function executeResultSet()
  {
    if ($this->stmt === null) {
      error_log('Cannot get result set. No prepared statement exists.');
      return [];
    }

    if ($this->execute()) {
      return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return [];
  }

  // Get prepared statement (for manual fetching after execute)
  public function getStatement()
  {
    return $this->stmt;
  }

  // Get row count
  public function rowCount()
  {
    if ($this->stmt === null) {
      error_log('Cannot get row count. No prepared statement exists.');
      return 0;
    }

    return $this->stmt->rowCount();
  }

  public function lastInsertId()
  {
    if ($this->dbh === null) {
      error_log('Cannot get last insert ID. Database connection not established.');
      return 0;
    }

    return $this->dbh->lastInsertId();
  }

  // Begin transaction
  public function beginTransaction()
  {
    if ($this->dbh === null) {
      error_log('Cannot begin transaction. Database connection not established.');
      return false;
    }
    return $this->dbh->beginTransaction();
  }

  // Commit transaction
  public function commit()
  {
    if ($this->dbh === null) {
      error_log('Cannot commit transaction. Database connection not established.');
      return false;
    }
    return $this->dbh->commit();
  }

  // Rollback transaction
  public function rollback()
  {
    if ($this->dbh === null) {
      error_log('Cannot rollback transaction. Database connection not established.');
      return false;
    }
    return $this->dbh->rollBack();
  }
}

