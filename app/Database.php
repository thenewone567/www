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
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $dbname = DB_NAME;

  private $dbh;
  private $stmt;
  private $error;

  public function __construct()
  {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    // Create PDO instance
    try {
      $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      // Log error rather than echoing it directly
      error_log('Database Connection Error: ' . $this->error);
      // Still display an error message, but more user-friendly
      die('Database connection failed. Please check your configuration or contact an administrator.');
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

    if ($this->execute()) {
      return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return [];
  }

  // Get single record as object
  public function single()
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

