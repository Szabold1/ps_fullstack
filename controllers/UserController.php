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
}
