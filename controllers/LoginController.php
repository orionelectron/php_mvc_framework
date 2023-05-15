<?php
namespace orion\controllers;
use orion\core\Controller;
use orion\core\Request;
use orion\core\Response;

class LoginController extends Controller{
    public function index(Request $request, Response $response){
        echo $request->getBody();
    }
    public function authenticate(Request $request, Response $response){
        echo "<h1>login</h1>";
    }
}