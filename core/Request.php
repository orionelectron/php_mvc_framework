<?php

namespace orion\core;

class Request
{
    protected string $requestMethod;
    protected array $body = [];

    protected string $requestPath;
    protected string $content_type;
    protected string $content_length;
    protected string $authorization;
    protected string $accept_language;
    protected string $referer;

    protected array $cookies;
    public function __construct()
    {
        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD'] ?? '');
        //$this->filter_request();
        $this->sanitize_cookies();
        $this->cookies = $_COOKIE;
        $this->content_type = $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        $this->content_length = $_SERVER['HTTP_CONTENT_LENGTH'] ?? '';
        $this->authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $this->accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $this->referer = $_SERVER['HTTP_REFERER'] ?? '';
        $this->requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    }

    public function method()
    {
        return $this->requestMethod;
    }

    public function getRequestPath(){
        return $this->requestPath;
    }

    private function sanitize_cookies()
    {
        foreach ($_COOKIE as $key => $value) {
            $_COOKIE[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    }
    private function sanitize_get()
    {
        $sanitized_get = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, $_GET);

        return $sanitized_get;
    }
    private function sanitize_post()
    {
        $sanitized_post = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, $_POST);
        return $sanitized_post;
    }
    public function body()
    {
        if ($this->requestMethod === 'get') {
            $this->body = $this->sanitize_get();
        } else if ($this->requestMethod === 'post') {
            $post = $this->sanitize_post();
            $this->body = array_merge($post, $_FILES);
        }

        return $this->body;
    }

    public function addParam($params){
        $sanitized_params = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }, $params);
        $this->body = array_merge($this->body, $sanitized_params);
    }
    public function param($param_name, $default_value = null) {
        return isset($this->body[$param_name]) ? $this->body[$param_name] : $default_value;
    }


    public function cookies()
    {
        return $this->cookies;
    }

    public function getContentType()
    {
        return $this->content_type;
    }

    public function getContentLength()
    {
        return $this->content_length;
    }

    public function getAuthorization()
    {
        return $this->authorization;
    }

    public function getAcceptLanguage()
    {
        return $this->accept_language;
    }

    public function getReferer()
    {
        return $this->referer;
    }
    public function setContentType($value)
    {
        $this->content_type = $value;
    }

    public function setContentLength($value)
    {
        $this->content_length = $value;
    }

    public function setAuthorization($value)
    {
        $this->authorization = $value;
    }

    public function setAcceptLanguage($value)
    {
        $this->accept_language = $value;
    }

    public function setReferer($value)
    {
        $this->referer = $value;
    }
}
