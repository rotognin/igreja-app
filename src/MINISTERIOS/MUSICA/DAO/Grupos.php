<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class Grupos extends DAO
{
    private array $colunas = array(
        'gru_id',
        'gru_nome',
        'gru_sigla',
        'gru_situacao',
        'gru_observacoes'
    );

    private array $situacao = array(
        'A' => 'Ativa',
        'I' => 'Inativa'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getSituacao(string $situacao = ''): array|string
    {
        return ($situacao == '') ? $this->situacao : $this->situacao[$situacao];
    }

    public function get($gru_id): array
    {
        $registros = $this->getArray(["AND gru_id = ?", [$gru_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}
            FROM {$this->table('igreja_db', 'grupos')}
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'grupos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $gru_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'grupos'), $record);
        $sql .= " WHERE gru_id = ?";
        $args[] = $gru_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
