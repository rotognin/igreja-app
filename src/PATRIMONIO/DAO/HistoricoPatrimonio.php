<?php

namespace App\PATRIMONIO\DAO;

use Funcoes\Lib\DAO;

class HistoricoPatrimonio extends DAO
{
    private array $colunas = array(
        'hpa_id',
        'hpa_patrimonio_id',
        'hpa_campo',
        'hpa_anterior',
        'hpa_novo',
        'hpa_data_hora',
        'hpa_usuario'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($hpa_id): array
    {
        $registros = $this->getArray([" AND hpa_id = ?", [$hpa_id]]);
        return $registros[0] ?? [];
    }

    public function getPatrimonio($pat_id): array
    {
        $registros = $this->getArray([" AND hpa_patrimonio_id = ?", [$pat_id]]);
        return $registros;
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = <<<SQL
                SELECT 
                    {$campos} 
                FROM {$this->table('igreja_db', 'historico_patrimonio')} 
                WHERE 1=1 
        SQL;

        if ($where) {
            $sql .= "$where[0]";
        }
        return $sql;
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $query = $this->baseQuery($where);
        if ($limit) {
            $query = $this->paginate($query, $limit, $offset, $order);
        } else {
            if ($order) {
                $query .= " ORDER BY $order";
            }
        }

        $stmt = $this->default->prepare($query);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'historico_patrimonio'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }
}
