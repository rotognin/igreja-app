<?php

namespace App\SGC\DAO;

use Funcoes\Interfaces\Authenticatable;
use Funcoes\Lib\DAO;
use Funcoes\Lib\Request;

class Menu extends DAO
{
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

    public function getArray($where = [], $order = "p.prg_lft", $limit = null): array
    {
        $sql = "SELECT 
            p.prg_codigo,
            p.prg_descricao,
            p.prg_url,
            p.prg_ativo,
            p.prg_icone,
            p.prg_codigo_pai,
            p.prg_lft,
            p.prg_rgt,
            p.prg_nivel,
            p.prg_sequencia,
            p.aca_acao
        FROM {$this->table('igreja_db', 'programa')} p
        WHERE 1=1
        ";
        if ($where) {
            $sql .= "$where[0]";
        }
        if ($order) {
            $sql .= " ORDER BY $order";
        }
        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'programa'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $prg_codigo, array $record): int
    {
        unset($record['prg_codigo']);
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'programa'), $record);
        $sql .= " WHERE prg_codigo = ?";
        $args[] = $prg_codigo;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function delete($prg_codigo)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'programa')} WHERE prg_codigo = ?";
        $stmt = $this->default->prepare($sql);
        $stmt->execute([$prg_codigo]);
        return $stmt->rowCount();
    }

    public function recalcularArvore()
    {
        $lft = 0;
        $rgt = 0;
        $nivel = 0;

        foreach ($this->getArray([' AND prg_codigo_pai = 0'], 'p.prg_sequencia') as $programa) {
            $lft = $rgt + 1;
            $rgt++;
            $programa['prg_lft'] = $lft;
            $programa['prg_nivel'] = $nivel;
            [$lft, $rgt] = $this->recalcularFilhos($programa['prg_codigo'], $lft, $rgt, $nivel + 1);
            $programa['prg_rgt'] = $rgt;
            $this->update($programa['prg_codigo'], $programa);
        }
    }

    public function recalcularFilhos($prg_codigo_pai, $lft, $rgt, $nivel)
    {
        $programas = $this->getArray([' AND p.prg_codigo_pai = ?', [$prg_codigo_pai]], 'p.prg_sequencia', null, [$prg_codigo_pai]);

        if (empty($programas)) {
            return [$lft, $rgt + 1];
        }
        foreach ($programas as $programa) {
            $lft = $lft + 1;
            $rgt++;
            $programa['prg_lft'] = $lft;
            $programa['prg_nivel'] = $nivel;
            [$lft, $rgt] = $this->recalcularFilhos($programa['prg_codigo'], $lft, $rgt, $nivel + 1);
            $programa['prg_rgt'] = $rgt;
            $this->update($programa['prg_codigo'], $programa);
            $lft = $rgt;
        }
        return [$lft, $rgt + 1];
    }

    public static function loadFromRequest(Request $request): array | null
    {
        $dao = new Menu();
        $menu = $dao->getArray([" AND p.prg_ativo = 'S' AND p.prg_url LIKE ?", ["%{$request->server('SCRIPT_NAME')}"]]);

        if (empty($menu)) {
            return null;
        }
        return $menu[0];
    }
}
