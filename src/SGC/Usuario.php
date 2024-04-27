<?php

namespace App\SGC;

use Funcoes\Interfaces\Authenticatable;
use Funcoes\Interfaces\Notificavel;
use Funcoes\Lib\Notificacoes\TraitNotificavel;

class Usuario implements Authenticatable, Notificavel
{
    use TraitNotificavel;

    public array $data = [];
    private array $actions = [];

    public static function loadFromRequest(): Authenticatable | null
    {
        global $session;

        $dao = new \App\SGC\DAO\Usuario();
        $data = $dao->get($session->get('credentials.default', ''));
        if (!$data || $data['usu_ativo'] != 'S') {
            return null;
        }

        $usuario = new Usuario();
        unset($data['usu_senha']);
        $usuario->data = $data;

        $usuario->actions = [];
        foreach ($dao->getAcoes($data['usu_login']) as $action) {
            $usuario->actions[$action['aca_acao']] = true;
        }
        return $usuario;
    }

    public function getUsername(): string
    {
        return $this->data['usu_login'];
    }

    public function checkAction(string $action): bool
    {
        return isset($this->actions[$action]);
    }

    public function logout(): void
    {
        global $session;
        $session->destroy();
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function auth(string $username, string $password): ?array
    {
        $dao = new \App\SGC\DAO\Usuario();
        $data = $dao->get($username);

        if (!$data) {
            return null;
        }

        if ($data['usu_provedor_auth'] == 'interno' && !self::verifyPassword($password, $data['usu_senha'])) {
            return null;
        } elseif ($data['usu_provedor_auth'] == 'ad') {
            $provider = new \Funcoes\Lib\ADProvider();
            $perfil = $provider->authenticate($username, $password);
            if (!$perfil) {
                return null;
            }

            if (($perfil['useraccountcontrol'] & 2) == 2 || ($perfil['useraccountcontrol'] & 512) != 512) {
                return null;
            }

            $dao->update($username, [
                'usu_nome' => $perfil['displayname'],
                'usu_email' => $perfil['mail'],
                'usu_ramal' => $perfil['telephonenumber'],
            ]);
        }

        return $data;
    }

    public function destinosNotificacao(): array
    {
        return [
            'email' => $this->data['usu_email'],
            'sms' => $this->data['usu_celular'],
        ];
    }

    public function getTipoNotificavel(): string
    {
        return 'usuario';
    }

    public function getID(): string
    {
        return $this->getUsername();
    }
}
