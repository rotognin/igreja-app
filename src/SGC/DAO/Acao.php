<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Acao extends DAO
{
    private array $colunas = array(
        'aca_acao',
        'aca_descricao',
        'aca_grupo'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($aca_acao): array
    {
        $acoes = $this->getArray(["AND aca_acao = ?", [$aca_acao]]);
        return $acoes[0] ?? [];
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(a.aca_acao) AS total 
                FROM {$this->table('igreja_db', 'acao')} a
                WHERE 1=1 ";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    private function baseQuery($where = [])
    {
        $campos = array_map(function ($campo) {
            return 'a.' . $campo;
        }, $this->colunas);

        $campos = implode(', ', $campos);
        $query = "SELECT 
            $campos
        FROM {$this->table('igreja_db', 'acao')} a
        WHERE 1=1
        ";

        if ($where) {
            $query .= "$where[0]";
        }
        return $query;
    }

    public function getArray($where = [], $order = "aca_grupo", $limit = null, $offset = '0'): array
    {
        $query = $this->baseQuery($where);
        if ($limit) {
            $query = $this->paginate($query, $limit, $offset, $order);
        } else {
            $query .= " ORDER BY $order";
        }

        $stmt = $this->default->prepare($query);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): string
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'acao'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $aca_acao, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'acao'), $record);
        $sql .= " WHERE aca_acao = ?";
        $args[] = $aca_acao;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function grupos(): array
    {
        $query = "
            SELECT distinct aca_grupo FROM {$this->table('igreja_db', 'acao')} ORDER BY aca_grupo
        ";
        $stmt = $this->default->query($query);
        return $stmt->fetchAll();
    }

    public function delete(string $aca_acao)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'acao')} WHERE aca_acao = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$aca_acao]);

        $sql = "DELETE FROM {$this->table('igreja_db', 'usuario_acao')} WHERE aca_acao = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$aca_acao]);
        return $stmt->rowCount();
    }
}
