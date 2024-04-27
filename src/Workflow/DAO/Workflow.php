<?php

namespace App\Workflow\DAO;

use Funcoes\Lib\DAO;

class Workflow extends DAO
{
    private array $colunas = array(
        'wrk_id',
        'wrk_tipo',
        'wrk_entidade',
        'wrk_entidade_id',
        'wrk_status',
        'wrk_data_ini',
        'wrk_data_fim'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($wrk_id): array
    {
        $workflows = $this->getArray(["AND wrk_id = ?", [$wrk_id]]);
        return $workflows[0] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos} 
            FROM {$this->table('igreja_db', 'workflow')} 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'workflow'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $wrk_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'workflow'), $record);
        $sql .= " WHERE wrk_id = ?";
        $args[] = $wrk_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
