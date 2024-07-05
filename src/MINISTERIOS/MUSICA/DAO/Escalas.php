<?php

namespace App\MINISTERIOS\MUSICA\DAO;

use Funcoes\Lib\DAO;

class Escalas extends DAO
{
    private array $colunas = array(
        'esc_id',
        'esc_titulo',
        'esc_data',
        'esc_hora',
        'esc_situacao',
        'esc_data_hora_gravacao',
        'esc_usuario_gravacao',
        'esc_data_hora_alteracao',
        'esc_usuario_alteracao',
        'esc_adiada_id' // ID da escala que foi criada a partir dessa que foi adiada
    );

    private array $situacao = array(
        'A' => 'Aberta',
        'F' => 'Fechada', // Não pode mais ser alterada
        'R' => 'Realizada',
        'C' => 'Cancelada',
        'D' => 'Adiada' // Será criada uma cópia dela para outra
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getSituacao(string $situacao = ''): string|array
    {
        return ($situacao == '') ? $this->situacao : $this->situacao[$situacao];
    }

    public function get($esc_id): array
    {
        $registros = $this->getArray(["AND esc_id = ?", [$esc_id]]);
        return $registros[0] ?? [];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT {$campos}
            FROM {$this->table('igreja_db', 'escalas')}
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'escalas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $esc_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'escalas'), $record);
        $sql .= " WHERE esc_id = ?";
        $args[] = $esc_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete($esc_id)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'escalas')} WHERE esc_id = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$esc_id]);
        return $stmt->rowCount();
    }
}
