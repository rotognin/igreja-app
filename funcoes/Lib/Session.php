<?php

namespace Funcoes\Lib;

class Session
{
    private array $bag = [];

    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }

        foreach ($_SESSION as $path => $value) {
            $keys = explode('.', $path);
            $entry = &$this->bag;
            foreach ($keys as $key) {
                if (!isset($entry[$key])) {
                    $entry[$key] = [];
                    $entry = &$entry[$key];
                }
            }
            $entry = $value;
        }

        $this->clearFlashMessages();
    }

    public function destroy()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public function get($path, $defaultValue = "")
    {
        $keys = explode(".", $path);
        $value = $this->bag;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $defaultValue;
            }
        }
        return $value;
    }

    public function set($path, $value)
    {
        $keys = explode(".", $path);
        $bag = &$this->bag;
        foreach ($keys as $key) {
            if (!isset($bag[$key])) {
                $bag[$key] = [];
            }
            $bag = &$bag[$key];
        }
        $bag = $value;
        $_SESSION[$path] = $value;
    }

    public function unset($path)
    {
        $keys = explode(".", $path);
        $bag = &$this->bag;
        foreach ($keys as $key) {
            if (isset($bag[$key])) {
                $bag = &$bag[$key];
            } else {
                return;
            }
        }
        unset($bag);
        unset($_SESSION[$path]);
    }

    public function check($path): bool
    {
        $keys = explode(".", $path);
        $value = $this->bag;
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return false;
            }
        }
        return true;
    }

    public function append($path, $value)
    {
        $values = $this->get($path, []);
        $values[] = $value;
        $this->set($path, $values);
    }

    public function flash($type, $msg = "")
    {
        if (empty($msg)) {
            return $this->get("flash.$type");
        }

        $this->append("flash.$type", $msg);
    }

    public function clearFlashMessages()
    {
        foreach (array_keys($_SESSION) as $key) {
            if (substr($key, 0, "6") == "flash." || $key == "previous") {
                unset($_SESSION[$key]);
            }
        }
    }
}
