<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Estados extends DAO
{
    private array $colunas = array(
        'id',
        'nome',
        'sigla',
        'codigo',
        'id_pais'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($sigla): array
    {
        $estados = $this->getArray(["AND sigla = ?", [$sigla]]);
        return $estados[0] ?? [];
    }

    public function getNome($sigla)
    {
        $estados = $this->getArray(["AND sigla = ?", [$sigla]]);
        return $estados[0]['nome'] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}
        FROM {$this->table('igreja_db', 'estados')}
        WHERE 1=1 
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

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'estados'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'estados'), $record);
        $sql .= " WHERE id = ?";
        $args[] = $id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
