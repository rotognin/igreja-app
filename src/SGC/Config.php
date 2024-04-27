<?php

namespace App\SGC;

class Config extends \Funcoes\Lib\Config
{
    public function __construct()
    {
        parent::__construct();
        $this->bag = array_merge($this->bag, [
            'module' => [
                'title' => 'SGC',
                'header_links' => [],
            ],
        ]);
    }
}
