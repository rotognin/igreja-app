<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Ministerios extends DAO
{
    private array $colunas = array(
        'min_id',
        'min_nome',
        'min_sigla',
        'min_ativo',
        'min_data_inc',
        'min_usu_inc',
        'min_data_alt',
        'min_usu_alt',
        'min_data_exc',
        'min_usu_exc'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($min_id): array
    {
        $registros = $this->getArray(["AND min_id = ?", [$min_id]]);
        return $registros[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(min_id) AS total 
                    FROM {$this->table('igreja_db', 'ministerios')} 
                WHERE min_usu_exc IS NULL";

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
                FROM {$this->table('igreja_db', 'ministerios')}
                WHERE min_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'ministerios'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $min_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'ministerios'), $record);
        $sql .= " WHERE min_id = ?";
        $args[] = $min_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
