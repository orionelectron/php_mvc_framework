<?php

namespace orion\core;
use orion\core\Router;



class Application
{
    public Router $router;
    public Request $request;

    public Response $response;
    public static Application $app;

    public Session $session;

    public  Database $db;

    public string $APP_ROOT;



    public function __construct(array $config)
    {
        $this->request = new Request();
        $this->response = new Response();
        $app = $this;
        $this->router = new Router($app, $this->request, $this->response);
        $this->session = new Session();
        $this->db = new Database($config);
        $this->APP_ROOT = $_ENV["APP_ROOT"];
        
    }

    public function get($path, $callback)
    {
        $this->router->get($path, $callback);
    }

    public function post($path, $callback)
    {
        $this->router->post($path, $callback);
    }

    public function run()
    {
        $this->router->route();
    }
}
