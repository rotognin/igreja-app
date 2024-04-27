<?php

namespace App\SGC\DAO;

use App\SGC\Usuario;
use Funcoes\Lib\DAO;

class LogPrograma extends DAO
{
    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function getMaisAcessados($where, $limit = 5)
    {
        $sql = "SELECT p.prg_descricao, l.prg_codigo, COUNT(l.prg_codigo) as Acessos, p.prg_url, p.prg_icone, p.prg_codigo_pai 
            FROM {$this->table('igreja_db', 'log_programa')} l
            LEFT JOIN {$this->table('igreja_db', 'programa')} p ON l.prg_codigo = p.prg_codigo
            WHERE 1=1 ";

        if ($where) {
            $sql .= "$where[0]";
        }

        $sql .= " GROUP BY l.prg_codigo, p.prg_descricao, p.prg_url, p.prg_icone, p.prg_codigo_pai 
            ORDER BY Acessos DESC
            LIMIT 0, $limit";

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(log_codigo) AS total 
                FROM {$this->table('igreja_db', 'log_programa')} l
                INNER JOIN {$this->table('igreja_db', 'usuario')} u ON (l.usu_login = u.usu_login)
                INNER JOIN {$this->table('igreja_db', 'programa')} p ON (l.prg_codigo = p.prg_codigo)
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
        $query = "SELECT 
            l.log_codigo,
            l.log_datahora,
            l.log_navegador,
            l.log_ip,
            u.usu_nome,
            u.usu_login,
            p.prg_codigo,
            p.prg_descricao,
            p.prg_url,
            p.prg_ativo,
            p.prg_icone,
            p.prg_codigo_pai
        FROM {$this->table('igreja_db', 'log_programa')} l
        INNER JOIN {$this->table('igreja_db', 'usuario')} u ON (l.usu_login = u.usu_login)
        INNER JOIN {$this->table('igreja_db', 'programa')} p ON (l.prg_codigo = p.prg_codigo)
        WHERE 1=1
        ";

        if ($where) {
            $query .= "$where[0]";
        }
        return $query;
    }

    public function getArray($where = [], $order = "log_datahora desc", $limit = "", $offset = '0')
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

    public function insert(array $record): int
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'log_programa'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public static function log(array $menu, Usuario $user)
    {
        global $request;

        $log = new LogPrograma();

        $ultimo = $log->getArray(["AND l.usu_login = ?", [$user->getUsername()]], "log_datahora desc", 1);

        if (!empty($ultimo) && $ultimo[0]['prg_codigo'] == $menu['prg_codigo']) {
            return;
        }

        $log->insert([
            'prg_codigo' => $menu['prg_codigo'],
            'usu_login' => $user->getUsername(),
            'log_datahora' => date('Y-m-d H:i:s'),
            'log_ip' => $request->server('REMOTE_ADDR'),
            'log_navegador' => $request->server('HTTP_USER_AGENT'),
        ]);
    }
}
