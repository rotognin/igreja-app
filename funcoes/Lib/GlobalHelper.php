<?php

namespace Funcoes\Lib;

class GlobalHelper
{
    protected $request;
    protected $response;
    protected $session;

    public function __construct()
    {
        global $request;
        global $response;
        global $session;

        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
    }
}
