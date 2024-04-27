<?php

namespace App\Workflow\DAO;

use Funcoes\Lib\DAO;

class Passo extends DAO
{
    public array $colunas = array(
        'pas_id',
        'pas_workflow_id',
        'pas_passo',
        'pas_titulo',
        'pas_descricao',
        'pas_minimo_aprovacoes',
        'pas_minimo_rejeicoes',
        'pas_data_ini',
        'pas_data_fim',
        'pas_status',
        'pas_documento_id',
        'pas_usuario',
        'pas_avancar',
        'pas_tipo_passo'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($pas_id): array
    {
        $passos = $this->getArray(["AND pas_id = ?", [$pas_id]]);
        return $passos[0] ?? [];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos} 
            FROM {$this->table('igreja_db', 'workflow_passos')} 
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'workflow_passos'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $pas_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'workflow_passos'), $record);
        $sql .= " WHERE pas_id = ?";
        $args[] = $pas_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
