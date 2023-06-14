<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Membros extends DAO
{
    private array $colunas = array(
        'mem_id',
        'mem_nome',
        'mem_telefone',
        'mem_data_inc',
        'mem_usu_inc',
        'mem_data_alt',
        'mem_usu_alt',
        'mem_data_exc',
        'mem_usu_exc',
        'mem_email',
        'mem_endereco',
        'mem_numero',
        'mem_bairro',
        'mem_cidade',
        'mem_estado',
        'mem_cep',
        'mem_familia_id',
        'mem_complemento'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($mem_id): array
    {
        $membros = $this->getArray(["AND mem_id = ?", [$mem_id]]);
        return $membros[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(mem_id) AS total 
                FROM {$this->table('igreja_db', 'membros')} 
                WHERE mem_usu_exc IS NULL";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    public function baseQuery($where)
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}, f.fam_nome
        FROM {$this->table('igreja_db', 'membros')} 
        LEFT JOIN {$this->table('igreja_db', 'familias')} f ON f.fam_id = mem_familia_id 
        WHERE mem_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'membros'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $mem_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'membros'), $record);
        $sql .= " WHERE mem_id = ?";
        $args[] = $mem_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
