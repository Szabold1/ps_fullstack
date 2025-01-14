<?php

namespace Framework;

use Controllers\UserController;
use Framework\Response;

class Router
{
    private static $routes = [
        "POST" => [
            "/register" => [
                "controller" => UserController::class,
                "method" => "register",
                "auth" => 'guest'
            ],
            "/login" => [
                "controller" => UserController::class,
                "method" => "login",
                "auth" => 'guest'
            ],
            "/logout" => [
                "controller" => UserController::class,
                "method" => "logout",
                "auth" => 'user'
            ],
            "/profile/edit" => [
                "controller" => UserController::class,
                "method" => "editProfile",
                "auth" => 'user'
            ],
        ],
        "GET" => [
            "/" => [
                "controller" => UserController::class,
                "method" => "index",
                "auth" => 'guest'
            ],
            "/register" => [
                "controller" => UserController::class,
                "method" => "pageRegister",
                "auth" => 'guest'
            ],
            "/login" => [
                "controller" => UserController::class,
                "method" => "pageLogin",
                "auth" => 'guest'
            ],
            "/profile" => [
                "controller" => UserController::class,
                "method" => "pageProfile",
                "auth" => 'user'
            ],
            "/profile/edit" => [
                "controller" => UserController::class,
                "method" => "pageEditProfile",
                "auth" => 'user'
            ],
        ]
    ];

    public static function route()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $route = Router::$routes[$method][$path] ?? null;
        if ($route) {
            // check if the user is authorized to access the route
            $auth = $route["auth"];
            $isLoggedIn = Session::get('user');
            if ($auth === 'guest' && $isLoggedIn) {
                redirect('/profile');
            }
            if ($auth === 'user' && !$isLoggedIn) {
                redirect('/');
            }

            // call the controller method if it exists
            $controller = new $route["controller"]();
            $controllerMethod = $route["method"];
            if (method_exists($controller, $controllerMethod)) {
                $controller->$controllerMethod();
                exit;
            }

            // if the controller method does not exist, return an error
            http_response_code(Response::$INTERNAL_SERVER_ERROR);
            loadView('error', ['message' => 'Belső kiszolgálóhiba']);
            exit;
        }

        // if the route is not found, return an error
        http_response_code(Response::$NOT_FOUND);
        loadView('error', ['message' => 'A keresett oldal nem található']);
        exit;
    }
}
