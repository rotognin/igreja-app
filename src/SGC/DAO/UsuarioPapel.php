<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class UsuarioPapel extends DAO
{
    private array $colunas = array(
        'usupap_id',
        'usupap_usu_login',
        'usupap_papel_id',
        'usupap_data_inc',
        'usupap_usu_inc',
        'usupap_data_exc',
        'usupap_usu_exc'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($usupap_id): array
    {
        $usuPapeis = $this->getArray(["AND usupap_id = ?", [$usupap_id]]);
        return $usuPapeis[0] ?? [];
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(usupap_id) AS total 
                FROM {$this->table('igreja_db', 'usuario_papel')} 
                WHERE usupap_usu_exc IS NULL  ";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    public function getArray($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}
            FROM {$this->table('igreja_db', 'usuario_papel')} 
            WHERE usupap_usu_exc IS NULL  
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

    public function getArrayCompleta($where = [], $order = null, $limit = null, $offset = '0'): array
    {
        $campos = array_map(function ($campo) {
            return 'x.' . $campo;
        }, $this->colunas);

        $campos = implode(', ', $campos);

        $sql = "SELECT 
            {$campos},
            u.usu_nome,
            p.pap_descricao
            FROM {$this->table('igreja_db', 'usuario_papel')} x
            LEFT JOIN {$this->table('igreja_db', 'usuario')} u ON x.usupap_usu_login = u.usu_login
            LEFT JOIN {$this->table('igreja_db', 'papel')} p ON x.usupap_papel_id = p.pap_id
            WHERE x.usupap_usu_exc IS NULL  
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

    public function insert(array $record): string
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'usuario_papel'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $usupap_id, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'usuario_papel'), $record);
        $sql .= " WHERE usupap_id = ?";
        $args[] = $usupap_id;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }
}
