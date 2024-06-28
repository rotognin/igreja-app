<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class Categorias extends DAO
{
    private array $colunas = array(
        'cam_id',
        'cam_descricao'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($cam_id): array
    {
        $registros = $this->getArray(["AND cam_id = ?", [$cam_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}
            FROM {$this->table('igreja_db', 'categoria_musica')}
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

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'categoria_musica'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $cam_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'categoria_musica'), $record);
        $sql .= " WHERE cam_id = ?";
        $args[] = $cam_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete($cam_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'categoria_musica')} WHERE cam_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$cam_id]);
        return $stmt->rowCount();
    }
}
