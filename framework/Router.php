<?php

namespace Framework;

use Controllers\UserController;
use Framework\Response;

class Router
{
    private static $routes = [
        "POST" => [
            "/register" => ["controller" => UserController::class, "method" => "register"],
            "/login" => ["controller" => UserController::class, "method" => "login"],
            "/logout" => ["controller" => UserController::class, "method" => "logout"],
            "/profile/edit" => ["controller" => UserController::class, "method" => "editProfile"],
        ],
        "GET" => [
            "/" => ["controller" => UserController::class, "method" => "index"],
            "/register" => ["controller" => UserController::class, "method" => "pageRegister"],
            "/login" => ["controller" => UserController::class, "method" => "pageLogin"],
            "/profile" => ["controller" => UserController::class, "method" => "pageProfile"],
            "/profile/edit" => ["controller" => UserController::class, "method" => "pageEditProfile"],
        ]
    ];

    public static function route()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $route = Router::$routes[$method][$path] ?? null;
        // If the route is found, call the controller method
        if ($route) {
            $controller = new $route["controller"]();
            $controllerMethod = $route["method"];

            if (method_exists($controller, $controllerMethod)) {
                $controller->$controllerMethod();
                exit;
            }
            http_response_code(Response::$INTERNAL_SERVER_ERROR);
            loadView('error', ['message' => 'Belső kiszolgálóhiba']);
            exit;
        }

        // If the route is not found, return an error
        http_response_code(Response::$NOT_FOUND);
        loadView('error', ['message' => 'A keresett oldal nem található']);
        exit;
    }
}
