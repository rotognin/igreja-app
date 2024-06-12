<?php

namespace App\PATRIMONIO\DAO;

use Funcoes\Lib\DAO;

class CategoriaPatrimonio extends DAO
{
    private array $colunas = array(
        'cpa_id',
        'cpa_titulo',
        'cpa_descricao',
        'cpa_ativo'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($cpa_id): array
    {
        $registros = $this->getArray(["AND cpa_id = ?", [$cpa_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
                    {$campos}
                FROM {$this->table('igreja_db', 'categoria_patrimonio')}
                WHERE 1=1  
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

    public function montarArray(array $antes = []): array
    {
        $array = $antes;

        $categorias = $this->getArray([" AND cpa_ativo = ?", ["S"]]);

        foreach ($categorias as $cat) {
            $array[$cat['cpa_id']] = $cat['cpa_titulo'];
        }

        return $array;
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'categoria_patrimonio'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $cpa_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'categoria_patrimonio'), $record);
        $sql .= " WHERE cpa_id = ?";
        $args[] = $cpa_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete($cpa_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'categoria_patrimonio')} WHERE cpa_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$cpa_id]);
        return $stmt->rowCount();
    }
}
