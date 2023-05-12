<?php 
namespace orion\core\interfaces;

interface DatabaseInterface {
  public function connect();
  public function query($sql, $params = []);
  public function fetch($sql, $params = []);
  public function fetchAll($sql, $params = []);
  public function insert($table, $data);
  public function update($table, $data, $where);
  public function delete($table, $where);
}
