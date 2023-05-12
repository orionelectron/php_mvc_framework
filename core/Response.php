<?php

namespace orion\core;

class Response
{
    public function __construct()
    {
    }

    public function send($content, $params = [])
    {
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $content = str_replace("{{$key}}", $value, $content);
            }
        }
        echo $content;
        exit;
    }

    public function sendTest($params = [])
    {
        $content = "<!DOCTYPE html>
        <html>
        <head>
            <title>My Form</title>
            <style>
            .form-group {
                margin: 10px;
                display: flex;
                flex-direction: column;
            }
            
            .form-group label {
                margin-bottom: 5px;
            }
            
            .form-group input[type=text],
            .form-group input[type=email],
            .form-group input[type=password],
            .form-group textarea {
                padding: 5px;
                width: auto;
            }
            
            .form-group input[type=checkbox],
            .form-group input[type=radio] {
                width: auto;
            }
            
            .form-group .error-field {
                color: red;
            }
            
            </style>
            
        </head>
        <body>
            {form}
        </body>
        </html>
        ";
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $content = str_replace("{{$key}}", $value, $content);
            }
        }
        echo $content;
    }

    public function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function setHeader($name, $value)
    {
        header("$name: $value");
    }

    public function redirect($url, $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        exit();
    }

    public function setCookie($name, $value, $expires = 0, $path = '', $domain = '', $secure = false, $httponly = false)
    {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
    }

    public function deleteCookie($name)
    {
        setcookie($name, '', time() - 3600);
    }

    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType);
    }

    public function setContentLength($contentLength)
    {
        $this->setHeader('Content-Length', $contentLength);
    }

    public function setEncoding($encoding)
    {
        $this->setHeader('Content-Encoding', $encoding);
    }

    public function setStatus($statusCode)
    {
        http_response_code($statusCode);
    }
}
