<?php

function basePath(string $path = ''): string
{
    return __DIR__ . '/' . $path;
}

function inspect($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function inspectAndDie($data)
{
    inspect($data);
    die();
}

function loadPartial($name, $data = [])
{
    $path = basePath("views/partials/{$name}.php");

    if (file_exists($path)) {
        extract($data);
        require $path;
    } else {
        echo "Partial not found: {$name}";
    }
}

function loadView($name, $data = [])
{
    $path = basePath("views/{$name}.view.php");

    if (file_exists($path)) {
        extract($data);
        require $path;
    } else {
        echo "View not found: {$name}";
    }
}
