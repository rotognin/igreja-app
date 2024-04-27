<?php

namespace Funcoes\Lib;

class Config
{
    protected array $bag = [];

    public function __construct()
    {
        $this->bag = require __DIR__ . '/../../config.php';

        $idioma = $this->bag['app']['language'];
        putenv("LC_ALL=$idioma");
        setlocale(LC_ALL, $idioma);

        bindtextdomain($idioma, __DIR__ . '/../ressource/locale');
        textdomain($idioma);

        date_default_timezone_set($this->bag['app']['timezone']);
    }

    public function get($path, $default = "")
    {
        $keys = explode('.', $path);
        $value = $this->bag;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }
        return $value;
    }
}
