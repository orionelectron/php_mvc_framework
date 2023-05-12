<?php

use Dotenv\Dotenv;
use orion\core\Application;

use orion\core\Request;
use orion\core\Response;
use orion\models\Registerform;

use orion\controllers\LoginController;


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv =  Dotenv::createImmutable(str_replace("\\public", "", __DIR__));
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

$app->get('/signup', function (Request $request, Response $response) {
    $form = new Registerform(["username" => '', 'email' => '', 'password' => '', 'confirmPassword' => ''], );

    //$response->sendTest( ['form' => $form->render()]);
    $response->send($form, []);
});
$app->post('/', function (Request $request, Response $response) {
});
$app->get('/login/{username:string}/{userId:int}', [LoginController::class, "index"]);

$app->post(
    '/signup',

    function (Request $request, Response $response) {
        $form = new Registerform($request->body(), '/signup');
        //$field = new FormField("movies", 'select', 'ironman', [['label' => 'ironman', 'value' => 'ironman'], ['label' => 'ironman 2', 'value' => 'ironman2']]);
        // $form->addField($field);
        if (empty($form->validate())) {
            $form->save();
            $response->send(" The data has been registered!! ", []);
        } else {
            $response->send($form, []);
        }
    }
);


$app->run();
