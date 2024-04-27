<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Email extends DAO
{
    private array $colunas = array(
        'email_id',
        'email_usuario',
        'email_tipo',
        'email_ultimo_envio',
        'email_ativo',
        'email_tipo_relatorio',
        'email_erro_envio'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($email_id): array
    {
        $emails = $this->getArray(["AND email_id = ?", [$email_id]]);
        return $emails[0] ?? [];
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(email_id) AS total 
                FROM {$this->table('igreja_db', 'email')} 
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
        $campos = implode(', ', $this->colunas);
        $query = "SELECT 
            $campos
            FROM {$this->table('igreja_db', 'email')} 
            WHERE 1=1
        ";

        if ($where) {
            $query .= "$where[0]";
        }
        return $query;
    }

    public function getArray($where = [], $order = " email_id ASC ", $limit = null, $offset = '0'): array
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
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'email'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(int $email_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'email'), $record);
        $sql .= " WHERE email_id = ?";
        $args[] = $email_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
