<?php

namespace Framework;

use Controllers\UserController;
use Framework\Response;
use Framework\Helper;

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
        ],
        "PUT" => [
            "/profile/edit" => [
                "controller" => UserController::class,
                "method" => "editProfile",
                "auth" => 'user'
            ],
        ],
    ];

    public static function route()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // change $method to the value of the _method parameter if it exists
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = $_POST['_method'];
        }

        $route = Router::$routes[$method][$path] ?? null;
        if ($route) {
            // check if the user is authorized to access the route
            $auth = $route["auth"];
            $isLoggedIn = Session::get('user');
            if ($auth === 'guest' && $isLoggedIn) {
                Helper::redirect('/profile');
            }
            if ($auth === 'user' && !$isLoggedIn) {
                Helper::redirect('/');
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
            Helper::loadView('error', ['message' => 'Belső kiszolgálóhiba']);
            exit;
        }

        // if the route is not found, return an error
        http_response_code(Response::$NOT_FOUND);
        Helper::loadView('error', ['message' => 'A keresett oldal nem található']);
        exit;
    }
}
