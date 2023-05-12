<?php

use Dotenv\Dotenv;
use orion\core\Application;



require_once __DIR__ . '/vendor/autoload.php';

$dotenv =  Dotenv::createImmutable(str_replace("\\public", "", __DIR__) );
$dotenv->load();



$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ],
    'APP_ROOT' => $_ENV["APP_ROOT"]
];

$app = new Application($config);

$app->db->applyMigrations();
