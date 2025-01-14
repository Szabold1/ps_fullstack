<?php

namespace Controllers;

use Framework\Validate;
use Framework\Response;
use Framework\Session;
use Models\UserModel;

class UserController
{

    public function index()
    {
        loadView('home');
    }

    public function pageRegister()
    {
        loadView('register');
    }

    public function register()
    {
        // get the data from the request
        $email = $_POST['email'] ?? '';
        $nickname = $_POST['nickname'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = [];
        // validate the data
        if (!Validate::email($email)) {
            $errors['email'] = "Az email cím formátuma nem megfelelő";
        }
        if (!Validate::nickname($nickname)) {
            $errors['nickname'] = "A becenévnek tartalmaznia kell legalább 2 karaktert, valamint csak betűket és számokat tartalmazhat";
        }
        if (!Validate::birthdate($birthdate)) {
            $errors['birthdate'] = "A kor nem lehet kevesebb mint 10, vagy több mint 100";
        }
        if (!Validate::password($password)) {
            $errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        // if there are errors, show the register page with the errors
        if ($errors) {
            http_response_code(Response::$BAD_REQUEST);
            loadView('register', [
                'errors' => $errors,
                'user' => [
                    "email" => $email,
                    "nickname" => $nickname,
                    "birthdate" => $birthdate,
                ]
            ]);
            exit;
        }

        // create the user
        $data = [
            "email" => $email,
            "nickname" => $nickname,
            "birthdate" => $birthdate,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ];
        $userModel = new UserModel();
        $userModel->create($data);

        loadView('register', [
            'successMsg' => "A regisztráció sikeres volt!"
        ]);
        exit;
    }

    public function pageLogin()
    {
        loadView('login');
    }

    public function login()
    {
        // get the data from the request
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = [];
        // validate the data
        if (!Validate::email($email)) {
            $errors['email'] = "Az email cím formátuma nem megfelelő";
        }
        if (!Validate::password($password)) {
            $errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        // if there are errors, show the login page with the errors
        if ($errors) {
            http_response_code(Response::$BAD_REQUEST);
            loadView('login', [
                'errors' => $errors,
                'user' => [
                    "email" => $email,
                ]
            ]);
            exit;
        }

        // login the user
        $data = [
            "email" => $email,
            "password" => $password
        ];
        $userModel = new UserModel();
        $user = $userModel->login($data);

        Session::set('user', [
            'id' => $user->id,
            'email' => $user->email,
            'nickname' => $user->nickname,
            'birthdate' => $user->birthdate
        ]);

        redirect('/profile');
    }

    public function pageProfile()
    {
        $user = Session::get('user');
        if (!$user) {
            redirect('/login');
        }

        loadView('profile', ['user' => $user]);
    }

    public function logout()
    {
        // delete all session data
        Session::destroy();

        // delete the session cookie
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        redirect('/login');
    }
}
