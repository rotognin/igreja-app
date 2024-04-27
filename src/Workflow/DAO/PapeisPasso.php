<?php

namespace App\Workflow\DAO;

use Funcoes\Lib\DAO;

class PapeisPasso extends DAO
{
    public array $colunas = array(
        'pappas_id',
        'pappas_passo_id',
        'pappas_papel_id'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($pappas_id): array
    {
        $papPasso = $this->getArray(["AND pappas_id = ?", [$pappas_id]]);
        return $papPasso[0] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos} 
            FROM {$this->table('igreja_db', 'papeis_passo')} 
            WHERE 1=1 
        ";

        if ($where) {
            $sql .= "$where[0]";
        }

        if ($order) {
            $sql .= " ORDER BY $order";
        }

        if ($limit) {
            //$sql .= " LIMIT $limit"; mysql

            if (!$order) {
                $sql .= " ORDER BY 1 ASC ";
            }

            $sql .= " LIMIT $offset, $limit ";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'papeis_passo'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $pappas_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'papeis_passo'), $record);
        $sql .= " WHERE pappas_id = ?";
        $args[] = $pappas_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
