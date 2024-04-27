<?php

namespace Funcoes\DAO;

use Funcoes\Lib\DAO;

class Notificacao extends DAO
{
    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($not_id)
    {
        $notificacoes = $this->getArray([" AND n.not_id = ?", [$not_id]]);
        return $notificacoes[0] ?? [];
    }

    private function baseQuery(array $where)
    {
        $sql = "SELECT 
            n.not_id,
            n.not_tipo,
            n.not_icone,
            n.not_url,
            n.not_mensagem,
            n.not_tipo_destino,
            n.not_id_destino,
            n.not_data_inc,
            n.not_data_lida
        FROM {$this->table('igreja_db', 'notificacoes')} n
        WHERE n.not_data_exc IS NULL";

        if ($where) {
            $sql .= "$where[0]";
        }

        return $sql;
    }

    public function getArray(array $where, $limit = null, $offset = 0, $orderBy  = 'n.not_data_inc DESC')
    {
        $sql = $this->baseQuery($where, $orderBy);
        if ($limit) {
            $sql = $this->paginate($sql, $limit, $offset, $orderBy);
        } else {
            $sql .= " ORDER BY $orderBy";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): string
    {
        $record['not_data_inc'] = date('Y-m-d H:i:s');
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'notificacoes'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(int $id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'notificacoes'), $record);
        $sql .= " WHERE not_id = ?";
        $args[] = $id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete(int $id)
    {
        $this->update($id, ['not_data_exc' => date('Y-m-d H:i:s')]);
    }
}
