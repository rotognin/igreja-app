<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class Musicas extends DAO
{
    private array $colunas = array(
        'mus_id',
        'mus_nome',
        'mus_artista',
        'mus_link',
        'mus_situacao',
        'mus_categoria_id'
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

    public function get($mus_id): array
    {
        $registros = $this->getArray(["AND mus_id = ?", [$mus_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}, cam_id, cam_descricao
            FROM {$this->table('igreja_db', 'musicas')}
            LEFT JOIN {$this->table('igreja_db', 'categoria_musica')}
                ON mus_categoria_id = cam_id
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'musicas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $mus_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'musicas'), $record);
        $sql .= " WHERE mus_id = ?";
        $args[] = $mus_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
