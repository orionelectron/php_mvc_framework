<?php

namespace orion\core;

use FFI\Exception;
use orion\core\interfaces\DatabaseInterface;

abstract class Model
{
    protected $tableName = "";
    protected const RULE_REQUIRED = "required";
    protected const RULE_MIN = "min";
    protected const RULE_MAX = "max";
    protected array $errors = [];
    protected const RULE_EMAIL = "email";

    protected const RULE_SHOULD_MATCH = "should_match";
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }


    

    public function rules(): array
    {
        return [];
    }

    public function addError($attribute, $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function labels(): array{
        return [];
    }

    public function validate()
    {
        $valid = true;
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute} ?? null;
            foreach ($rules as $rule) {
                $ruleName = '';
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                } else {
                    $ruleName = $rule;
                }
                // print_r($ruleName);
                //echo "<br/>";
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, ($this->labels()[$attribute] ?? $attribute) . " is Required!!");
                  
                    $valid = false;
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, ($this->labels()[$attribute] ?? $attribute) . " is not a valid email");
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule[1]) {
                    $this->addError($attribute,  ($this->labels()[$attribute] ?? $attribute) . " has to be a minimum " . $rule[1]);
                    $valid = false;
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule[1]) {
                    $this->addError($attribute,  ($this->labels()[$attribute] ?? $attribute) . " has to be a maximum " . $rule[1]);
                    $valid = false;
                }
                if ($ruleName === self::RULE_SHOULD_MATCH && $this->{$attribute} !== $this->{$rule[1]}) {
                    $this->addError($attribute, ($this->labels()[$attribute] ?? $attribute) . " does not match with  " . $rule[1]);
                    $valid = false;
                }
            }
        }
        if (empty($this->errors)) {

            return [];
        }

        return $this->errors;
    }

    public abstract  function attributes(): array;
 
}
