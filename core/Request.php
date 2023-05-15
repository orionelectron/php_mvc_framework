<?php

namespace orion\core;

class Request
{
    protected string $requestMethod;
    protected array $body = [];

    protected string $requestPath;
    protected array $headers = [];
    protected array $cookies = [];

    public function __construct()
    {
        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? '');
        $this->requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $this->headers = $this->getHeaders();
        $this->cookies = $this->getCookies();
        $this->body = $this->getBody();
    }

    public function method()
    {
        return $this->requestMethod;
    }

    public function getRequestPath()
    {
        return $this->requestPath;
    }

    public function body()
    {
        return $this->body;
    }

    public function addParam($params)
    {
        $sanitized_params = $this->sanitizeParams($params);
        $this->body = array_merge($this->body, $sanitized_params);
    }

    public function param($param_name, $default_value = null)
    {
        return isset($this->body[$param_name]) ? $this->body[$param_name] : $default_value;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function cookies()
    {
        return $this->cookies;
    }

    private function sanitizeParams($params)
    {
        return array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, $params);
    }

    private function getHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header_name = str_replace('_', '-', ucwords(strtolower(substr($key, 5))),);
                $headers[$header_name] = $value;
            }
        }
        return $headers;
    }

    private function getCookies()
    {
        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        return $cookies;
    }

    private function getBody()
    {
        $body = [];
        if ($this->requestMethod === 'get') {
            $body = $this->sanitizeParams($_GET);
        } elseif ($this->requestMethod === 'post') {
            $post = $this->sanitizeParams($_POST);
            $files = $this->sanitizeParams($_FILES);
            $body = array_merge($post, $files);
        }
        return $body;
    }
}
