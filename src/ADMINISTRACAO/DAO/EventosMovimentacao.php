<?php

namespace App\ADMINISTRACAO\DAO;

use Funcoes\Lib\DAO;

class EventosMovimentacao extends DAO
{
    private array $colunas = array(
        'evemov_id',
        'evemov_descricao',
        'evemov_data_inc',
        'evemov_usu_inc',
        'evemov_data_alt',
        'evemov_usu_alt',
        'evemov_data_exc',
        'evemov_usu_exc'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($evemov_id): array
    {
        $eventos = $this->getArray(["AND evemov_id = ?", [$evemov_id]]);
        return $eventos[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(evemov_id) AS total 
                FROM {$this->table('igreja_db', 'eventos_movimentacao')} 
                WHERE evemov_usu_exc IS NULL";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}
        FROM {$this->table('igreja_db', 'eventos_movimentacao')} 
        WHERE evemov_usu_exc IS NULL 
        ";

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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'eventos_movimentacao'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $evemov_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'eventos_movimentacao'), $record);
        $sql .= " WHERE evemov_id = ?";
        $args[] = $evemov_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
