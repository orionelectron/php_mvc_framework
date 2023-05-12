<?php


namespace orion\models;

use orion\core\DBModel;

class User extends DBModel{
    protected string $tablename = 'user';
    public string $username;
    public string $email;
    public string $password;
    public string $confirmPassword;

    public function __construct(string $username="", string $email="", string $password="",string $confirmPassword=""){
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;

    }

    public function attributes(): array{
        return ["username", "email", "password"];
    }

    public function rules(): array{
        return [
            "username" => [
                self::RULE_REQUIRED,
                
            ],
            "email" => [
                self::RULE_REQUIRED,
                self::RULE_EMAIL
            ],
            "password" => [
                self::RULE_REQUIRED,
                [self::RULE_MIN, 6],
                [self::RULE_MAX, 20] 
            ],
            "confirmPassword" => [
                self::RULE_REQUIRED,
                [self::RULE_MIN, 6],
                [self::RULE_MAX, 20],
                [self:: RULE_SHOULD_MATCH, "password"] 
            ]
            
            ];
    }


}
