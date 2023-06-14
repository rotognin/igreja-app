<?php

namespace Funcoes\Lib;

class DBManager
{
    private $connections = [];

    public function connect($connectionName)
    {
        global $config;
        $cfg = $config->get("db.{$connectionName}");
        $pdo = new \PDO($cfg['dsn'], $cfg['username'], $cfg['password'], $cfg['options']);
        $this->connections[$connectionName] = $pdo;
        return $pdo;
    }

    public function get($connectionName)
    {
        if (empty($this->connections[$connectionName])) {
            $this->connect($connectionName);
        }
        return $this->connections[$connectionName];
    }
}
