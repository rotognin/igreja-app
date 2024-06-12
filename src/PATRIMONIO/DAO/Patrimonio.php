<?php

namespace App\PATRIMONIO\DAO;

use Funcoes\Lib\DAO;

class Patrimonio extends DAO
{
    private array $colunas = array(
        'pat_id',
        'pat_descricao',
        'pat_categoria_id',
        'pat_marca',
        'pat_especificacao',
        'pat_tipo_entrada',
        'pat_nf_serie',
        'pat_nf_numero',
        'pat_nf_data',
        'pat_nf_valor',
        'pat_nf_chave',
        'pat_valor_estimado',
        'pat_data_entrada',
        'pat_usu_entrada',
        'pat_observacoes',
        'pat_quantidade',
        'pat_conservacao',
        'pat_usu_responsavel',
        'pat_ativo'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($pat_id): array
    {
        $registros = $this->getArray(["AND p.pat_id = ?", [$pat_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $colunas = array_map(function ($campo) {
            return 'p.' . $campo;
        }, $this->colunas);

        $campos = implode(', ', $colunas);

        $sql = <<<SQL
                SELECT 
                    {$campos},
                    c.cpa_titulo
                FROM {$this->table('igreja_db', 'patrimonio')} p
                LEFT JOIN {$this->table('igreja_db', 'categoria_patrimonio')} c
                    ON c.cpa_id = p.pat_categoria_id
                WHERE p.pat_ativo = 'S' 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'patrimonio'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $pat_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'patrimonio'), $record);
        $sql .= " WHERE pat_id = ?";
        $args[] = $pat_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
