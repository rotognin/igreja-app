<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Programa extends DAO
{
    private array $colunas = array(
        'prg_codigo',
        'prg_sequencia',
        'prg_descricao',
        'prg_url',
        'prg_icone',
        'prg_codigo_pai',
        'prg_lft',
        'prg_rgt',
        'prg_nivel',
        'prg_ativo',
        'aca_acao'
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($prg_codigo): array
    {
        $programas = $this->getArray(["AND prg_codigo = ?", [$prg_codigo]]);
        return $programas[0] ?? [];
    }

    public function getArray($where = [], $order = "", $limit = "", $offset = '0')
    {
        $campos = implode(', ', $this->colunas);

        $sql = "SELECT 
            {$campos}
            FROM {$this->table('igreja_db', 'programa')}
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
}
