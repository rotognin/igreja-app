<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class MusicaAnexos extends DAO
{
    private array $colunas = array(
        'mua_id',
        'mua_arquivo',
        'mua_musica_id',
        'mua_descricao',
        'mua_usuario_cadastro',
        'mua_data_hora_cadastro',
        'mua_tipo'
    );

    private array $tipo = array(
        'C' => 'Cifra',
        'L' => 'Letra',
        'S' => 'Slide',
        'P' => 'Partitura'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getTipo(string $tipo = ''): array|string
    {
        return ($tipo == '') ? $this->tipo : $this->tipo[$tipo];
    }

    public function get($mua_id): array
    {
        $registros = $this->getArray(["AND mua_id = ?", [$mua_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}, mus_id, mus_nome, mus_artista, cam_id, cam_descricao
            FROM {$this->table('igreja_db', 'musica_anexos')}
            INNER JOIN {$this->table('igreja_db', 'musicas')}
                ON mua_musica_id = mus_id
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

    public function getAnexosMusica($mus_id)
    {
        return $this->getArray([' AND mus_id = ?', [$mus_id]]);
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'musica_anexos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $mua_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'musica_anexos'), $record);
        $sql .= " WHERE mua_id = ?";
        $args[] = $mua_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
