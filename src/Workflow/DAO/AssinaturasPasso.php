<?php

namespace App\Workflow\DAO;

use Funcoes\Lib\DAO;

class AssinaturasPasso extends DAO
{
    public array $colunas = array(
        'asspas_id',
        'asspas_passo_id',
        'asspas_status',
        'asspas_usuario',
        'asspas_data'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($asspas_id): array
    {
        $assPasso = $this->getArray(["AND asspas_id = ?", [$asspas_id]]);
        return $assPasso[0] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos} 
            FROM {$this->table('igreja_db', 'assinaturas_passo')} 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'assinaturas_passo'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $asspas_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'assinaturas_passo'), $record);
        $sql .= " WHERE asspas_id = ?";
        $args[] = $asspas_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
