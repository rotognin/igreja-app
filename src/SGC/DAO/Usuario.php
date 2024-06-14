<?php

namespace App\SGC\DAO;

use Funcoes\Lib\DAO;

class Usuario extends DAO
{
    private array $colunas = array(
        'usu_login',
        'usu_senha',
        'usu_nome',
        'usu_email',
        'usu_ramal',
        'usu_celular',
        'usu_ativo',
        'usu_celular_whatsapp',
        'usu_provedor_auth',
    );

    public function __construct()
    {
        parent::__construct();
        $this->default = $this->dbManager->get('default');
    }

    public function get($usu_login): array
    {
        $usuarios = $this->getArray(["AND u.usu_login = ?", [$usu_login]]);
        return $usuarios[0] ?? [];
    }

    public function total($where)
    {
        $sql = "SELECT COUNT(usu_login) AS total 
                FROM {$this->table('igreja_db', 'usuario')} u
                WHERE 1=1 ";

        if ($where) {
            $sql .= "$where[0]";
        }

        $stmt = $this->default->prepare($sql);
        $stmt->execute($where[1] ?? []);
        $aRetorno = $stmt->fetchAll();
        return $aRetorno[0]['total'];
    }

    private function baseQuery($where)
    {
        $campos = array_map(function ($campo) {
            return 'u.' . $campo;
        }, $this->colunas);

        $campos = implode(', ', $campos);

        $sql = "SELECT 
            {$campos}
            , ue.emp_codigo,
            e.emp_nome
        FROM {$this->table('igreja_db', 'usuario')} u
        LEFT JOIN {$this->table('igreja_db', 'usuario_empresa')} ue 
            ON ue.usu_login = u.usu_login AND  ue.emp_padrao = 'S'
        LEFT JOIN {$this->table('igreja_db', 'empresa')} e 
            ON e.emp_codigo = ue.emp_codigo
        WHERE 1=1";

        if ($where) {
            $sql .= " $where[0]";
        }

        return $sql;
    }

    public function getArray($where = [], $order = "usu_login", $limit = null, $offset = '0'): array
    {
        $query = $this->baseQuery($where, $order);
        if ($limit) {
            $query = $this->paginate($query, $limit, $offset, $order);
        } else {
            $query .= " ORDER BY $order";
        }

        $stmt = $this->default->prepare($query);
        $stmt->execute($where[1] ?? []);
        return $stmt->fetchAll();
    }

    public function montarArray(array $antes = []): array
    {
        $array = $antes;

        $aUsuarios = $this->getArray([' AND usu_ativo = ?', ['S']]);

        foreach ($aUsuarios as $usu) {
            $array[$usu['usu_login']] = $usu['usu_nome'];
        }

        return $array;
    }

    public function insert(array $record): string
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'usuario'), $record);
        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $this->default->lastInsertId();
    }

    public function update(string $usu_login, array $record): int
    {
        [$sql, $args] = $this->preparedUpdate($this->table('igreja_db', 'usuario'), $record);
        $sql .= " WHERE usu_login = ?";
        $args[] = $usu_login;

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    public function getEmpresas($usu_login)
    {
        $sql = "SELECT 
            e.emp_codigo,
            e.emp_nome,
            ue.emp_padrao
        FROM {$this->table('igreja_db', 'empresa')} e
        INNER JOIN {$this->table('igreja_db', 'usuario_empresa')} ue 
            ON e.emp_codigo = ue.emp_codigo
        WHERE ue.usu_login = ?
        ORDER BY e.emp_codigo
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$usu_login]);
        return $stmt->fetchAll();
    }

    public function limparEmpresaPadrao($usu_login)
    {
        $sql = "UPDATE {$this->table('igreja_db', 'usuario_empresa')} 
        SET emp_padrao = 'N'
        WHERE usu_login = ?
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$usu_login]);
        return $stmt->rowCount();
    }

    public function adicionarEmpresa($usu_login, $empresa)
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'usuario_empresa'), [
            'usu_login' => $usu_login,
            'emp_codigo' => $empresa['emp_codigo'],
            'emp_padrao' => $empresa['emp_padrao']
        ]);

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return true;
    }

    public function removerEmpresa($usu_login, $emp_codigo)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'usuario_empresa')} 
        WHERE usu_login = ? AND emp_codigo = ?
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$usu_login, $emp_codigo]);
        return $stmt->rowCount();
    }

    public function getAcoes($usu_login): array
    {
        $sql = "SELECT 
            ua.aca_acao,
            a.aca_descricao,
            a.aca_grupo
        FROM {$this->table('igreja_db', 'acao')} a
        INNER JOIN {$this->table('igreja_db', 'usuario_acao')} ua 
            ON a.aca_acao = ua.aca_acao
        WHERE ua.usu_login = ?
        ORDER BY a.aca_grupo, a.aca_acao
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$usu_login]);
        return $stmt->fetchAll();
    }

    public function adicionarAcao($usu_login, $aca_acao)
    {
        [$sql, $args] = $this->preparedInsert($this->table('igreja_db', 'usuario_acao'), [
            'usu_login' => $usu_login,
            'aca_acao' => $aca_acao
        ]);

        $stmt = $this->default->prepare($sql);
        $stmt->execute($args);
        return true;
    }

    public function removerAcao($usu_login, $aca_acao)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'usuario_acao')} 
        WHERE usu_login = ? AND aca_acao = ?
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$usu_login, $aca_acao]);
        return $stmt->rowCount();
    }

    public function copiarAcoes($src, $dst)
    {
        $sql = "DELETE FROM {$this->table('igreja_db', 'usuario_acao')} 
        WHERE usu_login = ?
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$dst]);

        $sql = "INSERT INTO {$this->table('igreja_db', 'usuario_acao')} (usu_login, aca_acao)
        SELECT ?, aca_acao FROM {$this->table('igreja_db', 'usuario_acao')} WHERE usu_login = ?
        ";

        $stmt = $this->default->prepare($sql);
        $stmt->execute([$dst, $src]);
        return $stmt->rowCount();
    }
}
