<?php

namespace Funcoes\Lib;

use Funcoes\Interfaces\Authenticatable;

class AuthManager
{
    private static array $credentials = [];

    public function get(string $provider): Authenticatable | null
    {
        global $config;
        global $response;
        global $session;

        $cfg = $config->get("auth.{$provider}");

        if (empty($session->get("credentials.{$provider}"))) {
            return null;
        }

        if (!isset(self::$credentials[$provider])) {
            self::$credentials[$provider] = $cfg['user_class']::loadFromRequest();
        }

        return self::$credentials[$provider];
    }

    public function enforce(string $provider): Authenticatable | null
    {
        global $response;
        global $config;

        $user = $this->get($provider);
        if (!$user) {
            return $response->redirect($config->get("auth.{$provider}.login_page"));
        }
        return $user;
    }
}
