<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Papel extends DAO
{
    private array $colunas = array(
        'pap_id',
        'pap_descricao',
        'pap_data_inc',
        'pap_usu_inc',
        'pap_data_alt',
        'pap_usu_alt',
        'pap_data_exc',
        'pap_usu_exc'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($pap_id): array
    {
        $papeis = $this->getArray(["AND pap_id = ?", [$pap_id]]);
        return $papeis[0] ?? [];
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(pap_id) AS total 
                FROM {$this->table('igreja_db', 'papel')} 
                WHERE pap_usu_exc IS NULL ";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}
        FROM {$this->table('igreja_db', 'papel')} 
        WHERE pap_usu_exc IS NULL 
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

    public function insert(array $record): string
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'papel'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $pap_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'papel'), $record);
        $sql .= " WHERE pap_id = ?";
        $args[] = $pap_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
