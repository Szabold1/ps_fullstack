<?php

namespace Framework;

class Session
{
    public const ERRORS = 'errors';
    public const USER = 'user';
    public const SUCCESS = 'success';

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

    public static function setFlash(string $key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key)
    {
        $value = $_SESSION['_flash'][$key] ?? null;
        return $value;
    }

    public static function unsetFlashAll()
    {
        unset($_SESSION['_flash']);
    }
}
