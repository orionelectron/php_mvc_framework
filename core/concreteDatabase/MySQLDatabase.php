<?php
use orion\core\interfaces\DatabaseInterface;

class MySQLDatabase implements DatabaseInterface {
  private $host;
  private $username;
  private $password;
  private $dbname;
  private $pdo;

  public function __construct($host, $username, $password, $dbname) {
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    $this->dbname = $dbname;
  }

  public function connect() {
    $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4";

    try {
      $this->pdo = new PDO($dsn, $this->username, $this->password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
      throw new Exception("Connection failed: " . $e->getMessage());
    }
  }

  public function query($sql, $params = []) {
    try {
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch(PDOException $e) {
      throw new Exception("Query failed: " . $e->getMessage());
    }
  }

  public function fetch($sql, $params = []) {
    $stmt = $this->query($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function fetchAll($sql, $params = []) {
    $stmt = $this->query($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function insert($table, $data) {
    $keys = array_keys($data);
    $values = array_values($data);
    $placeholders = implode(",", array_fill(0, count($values), "?"));
    $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES ($placeholders)";
    $this->query($sql, $values);
    return $this->pdo->lastInsertId();
  }

  public function update($table, $data, $where) {
    $keys = array_keys($data);
    $values = array_values($data);
    $set = implode("=?,", $keys) . "=?";
    $sql = "UPDATE $table SET $set WHERE $where";
    $result = $this->query($sql, $values);
    return $result->rowCount();
  }

  public function delete($table, $where) {
    $sql = "DELETE FROM $table WHERE $where";
    $result = $this->query($sql);
    return $result->rowCount();
  }
}
