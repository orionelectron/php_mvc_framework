<?php

namespace orion\core;
use FFI\Exception;



class Router
{
    protected Application $app;
    private array $middlewares = [];
    private array $globalMiddlewares = [];
    private Request $request;
    private Response $response;


    protected array $routes = [];

    public function __construct(Application $app, Request $request, Response $response)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;
        $this->routes["get"] = [];
        $this->routes["post"] = [];
    }

    public function get($path, $callback)
    {
        $this->routes["get"][$path]=$callback;
    }

    public function post($path, $callback)
    {
        $this->routes["post"][$path]=$callback;
    }

    public function use(object $middleware): void
    {
        $this->globalMiddlewares[] = $middleware;
    }

    private function extractParams(string $path): array
    {
        $pattern = '/\{[a-z(:)A-Z]+\}/';
        $matches = [];
        preg_match_all($pattern, $path, $matches);
        if (!empty($matches[0])) {
            return array_map(fn ($match) => trim($match, '{}'), $matches[0]);
        }
        return [];
    }

    private function matchRegexPath(string $registeredPath, string $inputUri)
    {
        $route = $registeredPath;
        $input_uri = $inputUri;
        

        // Create a regular expression pattern from the given route
        $pattern = preg_replace_callback(
            '/\{([\w]+)(:[\w]+)?\}/',
            function ($matches) {
                $param = $matches[1];
                $type = $matches[2] ?? '';
                switch ($type) {
                    case ':int':
                        return "(?P<{$param}>[0-9]+)";
                    case ':string':
                        return "(?P<{$param}>[\w-]+)";
                    default:
                        return "(?P<{$param}>[^/]+)";
                }
            },
            $route
        );

        // Match the input URI against the pattern
        if (preg_match("#^{$pattern}$#", $input_uri, $matches)) {
            // Extract parameter values from named groups
            $params = array_intersect_key($matches, array_flip(array_filter(array_keys($matches), 'is_string')));
           
            // Validate parameter values using annotations
            foreach ($params as $key => $value) {
                $type = explode(':', $route)[1] ?? '';
               
                switch ($type) {
                    case ':int':
                        if (!is_numeric($value)) {
                           
                            throw new Exception("Invalid value for parameter '{$key}'");
                        }
                        break;
                    case ':string':
                        
                        if (!preg_match('/^[\w-]+$/', $value)) {
                            throw new Exception("Invalid value for parameter '{$key}'");
                        }
                        break;
                    default:
                        
                }
            }

            // Output the parameters as an associative array
            //print_r($params);
            return $params;
        } else {
            // echo "No match found";
            return null;
        }
    }
    private function handleRoute(string  $path, array $params, $callback): void
    {
        $middlewares = array_merge($this->globalMiddlewares,$this->middlewares[$path] ?? []);
        $this->applyMiddlewares($middlewares);
        
        if (is_callable($callback)) {
            //echo "callable";
            $this->app->request->addParam($params);
            call_user_func_array($callback, [$this->app->request, $this->app->response]);
        } else if (is_string($callback)) {
            // Add your code here to handle string callbacks
        }
        else{
            
          $callbackClass = new $callback[0]($this->app->APP_ROOT);
          $callbackMethod = $callback[1];
          
          $callbackClass->$callbackMethod($this->request, $this->response);
        }

    }
    private function applyMiddlewares($middlewares = [])
    {
        foreach ($middlewares as $middleware) {
           $middleware->handle($this->request, $this->response);
        }
    }

    public function route()
    {
        $requestMethod = $this->request->method();
        $requestPath = $this->request->getRequestPath();
       // echo basename(__DIR__);
        //echo basename($requestPath);
        $requestPath = str_replace($this->app->APP_ROOT . "/".basename($requestPath) , "", $requestPath);
        
        $routesList = $this->routes[$requestMethod];
        foreach ($routesList as $path => $callback) {
            
            $params = $this->extractParams($path);
            //print_r($params);

            if (empty($params)) {
                //echo "empty params Normal route";
                if ($path === $requestPath) {
                    $this->handleRoute($path, $params, $callback);
                    return;
                }
            } else {
                //echo "nonempty params regex route";
                $routingResult = $this->matchRegexPath($path, $requestPath);
                //print_r($routingResult);
                if ($routingResult !== null) {
                    $this->handleRoute($path, $routingResult, $callback);
                    return;
                }
            }
        }
        echo "<h1>404 Not found</h1>";
    }
}
