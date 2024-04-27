<?php

namespace Funcoes\Lib;

class Request
{
    private array $get, $post, $server, $headers;

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->server = $_SERVER ?? [];
        $this->headers = [];
        if ($headers = apache_request_headers()) {
            $this->headers = $headers;
        }

        unset($_POST, $_GET, $_SERVER);
    }

    public function getArray(int $filter = FILTER_DEFAULT): array
    {
        return $this->get;
    }

    public function get($key, $default = "", int $filter = FILTER_DEFAULT)
    {
        return htmlspecialchars(filter_var($this->get[$key] ?? $default, $filter));
    }

    public function postArray(int $filter = FILTER_DEFAULT): array
    {
        return array_map(function ($var) use ($filter) {
            return htmlspecialchars(filter_var($var, $filter));
        }, $this->post);
    }

    public function post($key, $default = "", int $filter = FILTER_DEFAULT)
    {
        if (is_array($this->post[$key] ?? '')) {
            return filter_var_array($this->post[$key] ?? $default, $filter);
        }

        return htmlspecialchars(filter_var($this->post[$key] ?? $default, $filter));
    }

    public function header($key, $default = "")
    {
        return $this->headers[$key] ?? $default;
    }

    public function server($key, $default = "")
    {
        return $this->server[$key] ?? $default;
    }
}
