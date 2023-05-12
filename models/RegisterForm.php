<?php

namespace orion\models;

use orion\core\Form;

class Registerform extends Form{
    protected string $tablename = 'user';
    public string $username;
    public string $email;
    public string $password;
    public string $confirmPassword;

    public function __construct($data, $path=""){
        parent::__construct($data, $path);
        $this->loadData($data);

    }

    public function labels(): array{
        return [
            'username' => "username",
            'email' => "Email",
            'password' => "Password",
            'confirmPassword' => "Confirm Password"
        ];
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