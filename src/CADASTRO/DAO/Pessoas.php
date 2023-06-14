<?php

namespace App\CADASTRO\DAO;

use Funcoes\Lib\DAO;

class Pessoas extends DAO
{
    private array $colunas = array(
        'pes_id',
        'pes_nome',
        'pes_telefone',
        'pes_email',
        'pes_endereco',
        'pes_numero',
        'pes_bairro',
        'pes_cidade',
        'pes_cep',
        'pes_complemento',
        'pes_observacao',
        'pes_familia_id',
        'pes_data_inc',
        'pes_usu_inc',
        'pes_data_alt',
        'pes_usu_alt',
        'pes_data_exc',
        'pes_usu_exc',
        'pes_estado'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($pes_id): array
    {
        $pessoas = $this->getArray(["AND pes_id = ?", [$pes_id]]);
        return $pessoas[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(pes_id) AS total 
                FROM {$this->table('igreja_db', 'pessoas')} 
                WHERE pes_usu_exc IS NULL";

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
        FROM {$this->table('igreja_db', 'pessoas')}
        LEFT JOIN {$this->table('igreja_db', 'familias')} f ON pes_familia_id = f.fam_id 
        WHERE pes_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'pessoas'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $pes_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'pessoas'), $record);
        $sql .= " WHERE pes_id = ?";
        $args[] = $pes_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
