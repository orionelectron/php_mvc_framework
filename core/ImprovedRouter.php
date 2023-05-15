<?php

namespace Orion\Core;

class Router
{
    protected Request $request;
    protected array $routes = [];
    protected string $currentPrefix;
    protected array $globalMiddlewares = [];
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function use($middleware)
    {
        array_push($this->globalMiddlewares, $middleware);
    }

    public function post(string $path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function group(string $prefix, callable $callback)
    {
        $oldPrefix = $this->currentPrefix;
        $this->currentPrefix .= $prefix;
        $callback($this);
        $this->currentPrefix = $oldPrefix;
    }

    public function dispatch()
    {
        $requestMethod = $this->request->method();
        $requestPath = $this->request->getRequestPath();
        $handler = null;
        $params = [];
        $middlewares = [];

        foreach ($this->routes as $route) {
            $pattern = $route['pattern'];
            $methods = $route['methods'];
            $callback = $route['handler'];
            $middlewares = $route['middlewares'];

            if (in_array($requestMethod, $methods) && preg_match($pattern, $requestPath, $matches)) {
                $handler = $callback;
                $params = array_slice($matches, 1);
                $middlewares = array_merge($this->globalMiddlewares, $middlewares);
                break;
            }
        }

        if ($handler === null) {
            // Handle 404 Not Found
            $this->error_404("Route Not found!!");
        } else {
            // Apply global middleware
            foreach ($middlewares as $middleware) {
                $middleware($this->request);
            }

            if (is_array($handler)) {
                [$controllerClass, $method] = $handler;
                $controller = new $controllerClass();
                call_user_func_array([$controller, $method], $params);
            } elseif (is_callable($handler)) {
                call_user_func_array($handler, $params);
            } else {
                // Invalid handler
                // ...
            }
        }
    }


    protected function addRoute(string $method, string $path, $handler, array $middlewares = [])
    {
        $pattern = $this->currentPrefix . $path;
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        // Replace route parameters with named capturing groups
        $pattern = preg_replace('/\{([^\}]+)\}/', '(?P<$1>[^\/]+)', $pattern);

        $this->routes[] = [
            'methods' => (array)$method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    protected function sanitizeParams(array $params): array
    {
        $sanitizedParams = [];
        foreach ($params as $key => $value) {
            $sanitizedParams[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $sanitizedParams;
    }

    protected function error_404($message){
        echo "404 " + $message;
    }
}
