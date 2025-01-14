<?php

namespace Framework;

class Helper
{
    public static function basePath(string $path = ''): string
    {
        return __DIR__ . '/../' . $path;
    }

    public static function inspect($data)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public static function inspectAndDie($data)
    {
        self::inspect($data);
        die();
    }

    public static function loadPartial($name, $data = [])
    {
        $path = self::basePath("app/views/partials/{$name}.php");

        if (file_exists($path)) {
            extract($data);
            require $path;
        } else {
            echo "Partial not found: {$name}";
        }
    }

    public static function loadView($name, $data = [])
    {
        $path = self::basePath("app/views/{$name}.view.php");

        if (file_exists($path)) {
            extract($data);
            require $path;
        } else {
            echo "View not found: {$name}";
        }
    }

    public static function redirect(string $path)
    {
        header("Location: {$path}");
        exit;
    }
}
