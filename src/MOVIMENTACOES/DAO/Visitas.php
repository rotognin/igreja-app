<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Visitas extends DAO
{
    private array $colunas = array(
        'vis_id',
        'vis_data',
        'vis_hora',
        'vis_observacao',
        'vis_data_inc',
        'vis_usu_inc',
        'vis_data_alt',
        'vis_usu_alt',
        'vis_data_exc',
        'vis_usu_exc',
        'vis_descricao',
        'vis_status',
        'vis_familia_id',
        'vis_titulo'
    );

    private array $situacoes = array(
        'A Realizar' => 'A Realizar',
        'Realizada'  => 'Realizada',
        'Cancelada'  => 'Cancelada'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getSituacoes(): array
    {
        return $this->situacoes;
    }

    public function get($vis_id): array
    {
        $visitas = $this->getArray(["AND vis_id = ?", [$vis_id]]);
        return $visitas[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(vis_id) AS total 
                FROM {$this->table('igreja_db', 'visitas')} 
                WHERE vis_usu_exc IS NULL";

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
            {$campos}, f.fam_nome
        FROM {$this->table('igreja_db', 'visitas')} 
        LEFT JOIN {$this->table('igreja_db', 'familias')} f ON f.fam_id = vis_familia_id 
        WHERE vis_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'visitas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $vis_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'visitas'), $record);
        $sql .= " WHERE vis_id = ?";
        $args[] = $vis_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
