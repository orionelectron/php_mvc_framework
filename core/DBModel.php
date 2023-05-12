<?php

namespace orion\core;

use orion\core\Model;
use PDO;
use PDOException;

 class DBModel extends Model
{
    protected string $tablename = "";
    public function tableName() :string
    {
        return $this->tablename;
    }
    public function attributes(): array{
        return [];
    }

    

    public function loadData($data = []){
        foreach($data as $key => $value){
            if (property_exists($this, $key)){
                $this->{$key} = $value;
            }
        }
    }

    public function save()
    {
        $attributes = $this->attributes();
        $params = [];
        $values = [];
        $setValues = [];

        foreach ($attributes as $attribute) {
            if ($this->{$attribute} !== null) {
                $params[] = $attribute;
                $values[] = $this->{$attribute};
                $setValues[] = "$attribute=?";
            }
        }

        $paramStr = implode(", ", $params);
        $valueStr = implode(", ", array_fill(0, count($values), "?"));
        $setValueStr = implode(", ", $setValues);

        $tableName = $this->tableName();
        $sql = "INSERT INTO $tableName ($paramStr) VALUES ($valueStr) ON DUPLICATE KEY UPDATE $setValueStr";
       // echo "<br> $sql";
        try {
            $stmt = self::prepare($sql);
            return $stmt->execute(array_merge($values, $values));
        } catch (PDOException $e) {
            echo "Exception " . $e;
            // handle the exception here, e.g. by logging the error or displaying a user-friendly message
            return false;
        }
    }

    public static function prepare($sql)
    {
        $pdo = new PDO($_ENV["DB_DSN"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);
        return $pdo->prepare($sql);
    }
}
