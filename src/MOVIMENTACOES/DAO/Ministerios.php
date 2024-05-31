<?php

namespace App\MOVIMENTACOES\DAO;

use Funcoes\Lib\DAO;

class Ministerios extends DAO
{
    private array $colunas = array(
        'mvm_id',
        'mvm_ministerio',
        'mvm_pessoa',
        'mvm_funcao',
        'mvm_data_inc',
        'mvm_usu_inc',
        'mvm_data_alt',
        'mvm_usu_alt',
        'mvm_data_exc',
        'mvm_usu_exc'
    );

    private array $funcoes = array(
        'L' => 'Líder',
        'V' => 'Vice-líder',
        'A' => 'Auxiliar',
        'P' => 'Participante'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getFuncao(string $funcao = ''): array|string
    {
        return ($funcao == '') ? $this->funcoes : $this->funcoes[$funcao];
    }

    public function get($mvm_id): array
    {
        $registros = $this->getArray(["AND mvm_id = ?", [$mvm_id]]);
        return $registros[0] ?? [];
    }

    public function total($where = [])
    {
        $sql = "SELECT COUNT(mvm_id) AS total 
                FROM {$this->table('igreja_db', 'mov_ministerios')} 
                WHERE mvm_usu_exc IS NULL";

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
        $colunas = array_map(function ($campo) {
            return 'm.' . $campo;
        }, $this->colunas);

        $campos = implode(', ', $colunas);

        $sql = "SELECT {$campos},
                min.min_nome, min.min_sigla, pes.pes_nome
            FROM {$this->table('igreja_db', 'mov_ministerios')} m
            LEFT JOIN {$this->table('igreja_db', 'ministerios')} min 
                ON m.mvm_ministerio = min.min_id
            LEFT JOIN {$this->table('igreja_db', 'pessoas')} pes 
                ON m.mvm_pessoa = pes.pes_id 
            WHERE mvm_usu_exc IS NULL 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'mov_ministerios'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $mvm_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'mov_ministerios'), $record);
        $sql .= " WHERE mvm_id = ?";
        $args[] = $mvm_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function deleteMinisterio(string $mvm_ministerio)
    {
        // Excluir todas as ligações de pessoas com um ministério específico
        $sql = "DELETE FROM {$this->table('igreja_db', 'mov_ministerios')} WHERE mvm_ministerio = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$mvm_ministerio]);
        return $stmt->rowCount();
    }

    public function deletePessoa(string $mvm_pessoa)
    {
        // Excluir todas as ligações de ministérios com uma pessoa específica
        $sql = "DELETE FROM {$this->table('igreja_db', 'mov_ministerios')} WHERE mvm_pessoa = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$mvm_pessoa]);
        return $stmt->rowCount();
    }
}
