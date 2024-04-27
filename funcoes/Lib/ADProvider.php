<?php

namespace Funcoes\Lib;

class ADProvider
{
    private array $config;

    public function __construct()
    {
        global $config;
        $this->config = [
            'host' => $config->get('auth.default.ad.host'),
            'port' => $config->get('auth.default.ad.port', 389),
            'domain' => $config->get('auth.default.ad.domain'),
            'user' => $config->get('auth.default.ad.user'),
            'password' => $config->get('auth.default.ad.password'),
        ];
    }

    private function connect($username, $password)
    {
        $conn = ldap_connect($this->config['host'], $this->config['port']);
        if ($conn) {
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

            $bind = ldap_bind($conn, $username, $password);
            if ($bind) {
                return $conn;
            }
        }
        return false;
    }

    private function profile($conn, $username)
    {
        $filter = "(&(sAMAccountName=$username))";
        $domainInfo = self::domainInfo($this->config['domain']);
        $result = ldap_search($conn, $domainInfo, $filter, ['samaccountname', 'useraccountcontrol', 'telephonenumber', 'mail', 'displayname']);
        if ($result === false) {
            return false;
        }
        $entries = ldap_get_entries($conn, $result);
        if ($entries === false) {
            return false;
        }

        if ($entries['count'] == 0) {
            return [];
        }

        return [
            'samaccountname' => $entries[0]['samaccountname'][0] ?? '',
            'useraccountcontrol' => $entries[0]['useraccountcontrol'][0] ?? 0,
            'telephonenumber' => $entries[0]['telephonenumber'][0] ?? '',
            'mail' => $entries[0]['mail'][0] ?? '',
            'displayname' => $entries[0]['displayname'][0] ?? '',
        ];
    }

    public function getUserProfile($username)
    {
        $conn = $this->connect($this->config['user'], $this->config['password']);
        if ($conn) {
            return $this->profile($conn, $username);
        }

        return false;
    }

    public function authenticate(string $username, string $password): ?array
    {
        $conn = $this->connect("$username@{$this->config['domain']}", $password);
        if ($conn) {
            $profile = $this->profile($conn, $username);
            if ($profile) {
                return $profile;
            }
        }
        return null;
    }

    private static function domainInfo($domain)
    {
        return implode(',', array_map(function ($e) {
            return "DC=$e";
        }, explode('.', $domain)));
    }
}
