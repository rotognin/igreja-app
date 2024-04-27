<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Familias extends DAO
{
    private array $colunas = array(
        'fam_id',
        'fam_nome',
        'fam_observacao',
        'fam_data_inc',
        'fam_usu_inc',
        'fam_data_alt',
        'fam_usu_alt',
        'fam_data_exc',
        'fam_usu_exc'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($fam_id): array
    {
        $familias = $this->getArray(["AND fam_id = ?", [$fam_id]]);
        return $familias[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(fam_id) AS total 
                FROM {$this->table('igreja_db', 'familias')} 
                WHERE fam_usu_exc IS NULL";

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
        FROM {$this->table('igreja_db', 'familias')}
        WHERE fam_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'familias'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $fam_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'familias'), $record);
        $sql .= " WHERE fam_id = ?";
        $args[] = $fam_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
