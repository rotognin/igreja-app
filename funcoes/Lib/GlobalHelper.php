<?php

namespace Funcoes\Lib;

class GlobalHelper
{
    protected $request;
    protected $response;
    protected $session;
    protected $config;

    public function __construct()
    {
        global $request;
        global $response;
        global $session;
        global $config;

        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->config = $config;
    }
}
