<?php

namespace App\Dashboard;

class Config extends \Funcoes\Lib\Config
{
    public function __construct()
    {
        parent::__construct();
        $this->bag = array_merge($this->bag, [
            'module' => [
                'title' => 'Dashboard',
                'header_links' => [
                    [
                        'title' => 'Dashboard',
                        'href' => '/dashboard',
                        'icon' => 'fas fa-dashboard',
                    ],
                ],
            ],
        ]);
    }
}
