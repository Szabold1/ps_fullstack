<?php

namespace Framework;

class Session
{
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
}
