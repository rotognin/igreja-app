<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class GrupoPessoas extends DAO
{
    private array $colunas = array(
        'grp_id',
        'grp_grupo_id',
        'grp_pessoa_id',
        'grp_observacoes'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($grp_id): array
    {
        $registros = $this->getArray(["AND grp_id = ?", [$grp_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}, 
                g.gru_id, g.gru_nome, g.gru_sigla, 
                p.pes_id, p.pes_nome
            FROM {$this->table('igreja_db', 'grupo_pessoas')}
                INNER JOIN {$this->table('igreja_db', 'grupos')} g
                    ON grp_grupo_id = g.gru_id
                INNER JOIN {$this->table('igreja_db', 'pessoas')} p
                    ON grp_pessoa_id = p.pes_id
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

    public function getPessoas($gru_id)
    {
        return $this->getArray([' AND grp_grupo_id = ?', [$gru_id]]);
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'grupo_pessoas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $grp_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'grupo_pessoas'), $record);
        $sql .= " WHERE grp_id = ?";
        $args[] = $grp_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(string $grp_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'grupo_pessoas')} WHERE grp_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$grp_id]);
        return $stmt->rowCount();
    }
}
