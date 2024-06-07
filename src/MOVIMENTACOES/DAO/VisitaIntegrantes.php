<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class VisitaIntegrantes extends DAO
{
    private array $colunas = array(
        'vin_id',
        'vin_visita_id',
        'vin_pessoa_id'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($vin_id): array
    {
        $visitas = $this->getArray(["AND vin_id = ?", [$vin_id]]);
        return $visitas[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(vin_id) AS total 
                FROM {$this->table('igreja_db', 'visita_integrantes')} 
                WHERE 1=1 ";

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

        $sql = <<<SQL
            SELECT {$campos}, p.pes_nome, v.vis_titulo 
            FROM {$this->table('igreja_db', 'visita_integrantes')} 
            LEFT JOIN {$this->table('igreja_db', 'pessoas')} p 
                ON p.pes_id = vin_pessoa_id 
            LEFT JOIN {$this->table('igreja_db', 'visitas')} v
                ON v.vis_id = vin_visita_id
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'visita_integrantes'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $vin_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'visita_integrantes'), $record);
        $sql .= " WHERE vin_id = ?";
        $args[] = $vin_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $vis_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'visita_integrantes')} WHERE vin_visita_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$vis_id]);
        return $stmt->rowCount();
    }
}
