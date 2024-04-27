<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Empresa extends DAO
{
    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($emp_codigo): array
    {
        $usuarios = $this->getArray(["AND emp_codigo = ?", [$emp_codigo]]);
        return $usuarios[0] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null): array
    {
        $sql = "SELECT 
            emp_codigo,
            emp_nome
        FROM {$this->table('igreja_db', 'empresa')}
        WHERE 1=1
        ";
        if ($where) {
            $sql .= "$where[0]";
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'empresa'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $emp_codigo, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'empresa'), $record);
        $sql .= " WHERE emp_codigo = ?";
        $args[] = $emp_codigo;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
