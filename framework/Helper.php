<?php

declare(strict_types=1);

namespace Framework;

class Helper
{
    public static function basePath(string $path = ''): string
    {
        return __DIR__ . '/../' . $path;
    }

    public static function inspect($data): void
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    public static function inspectAndDie($data): never
    {
        self::inspect($data);
        die();
    }

    public static function loadPartial($name, $data = []): void
    {
        $path = self::basePath("app/views/partials/{$name}.php");

        if (file_exists($path)) {
            extract($data);
            require $path;
        } else {
            echo "Partial not found: {$name}";
        }
    }

    public static function loadView($name, $data = []): never
    {
        $path = self::basePath("app/views/{$name}.view.php");

        if (file_exists($path)) {
            extract($data);
            require $path;
        } else {
            echo "View not found: {$name}";
        }

        exit;
    }

    public static function redirect(string $path): never
    {
        header("Location: {$path}");
        exit;
    }
}
