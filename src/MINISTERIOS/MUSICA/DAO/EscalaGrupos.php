<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class EscalaGrupos extends DAO
{
    private array $colunas = array(
        'esg_id',
        'esg_escala_id',
        'esg_grupo_id',
        'esg_data_hora_gravacao',
        'esg_usuario_gravacao'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($esg_id): array
    {
        $registros = $this->getArray(["AND esg_id = ?", [$esg_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}, 
                g.gru_id, g.gru_nome, g.gru_sigla, 
                e.esc_titulo
            FROM {$this->table('igreja_db', 'escala_grupos')}
                INNER JOIN {$this->table('igreja_db', 'grupos')} g
                    ON esg_grupo_id = g.gru_id
                INNER JOIN {$this->table('igreja_db', 'escalas')} e
                    ON esg_escala_id = e.esc_id
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'escala_grupos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $esg_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'escala_grupos'), $record);
        $sql .= " WHERE esg_id = ?";
        $args[] = $esg_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(string $esg_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'escala_grupos')} WHERE esg_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$esg_id]);
        return $stmt->rowCount();
    }
}
