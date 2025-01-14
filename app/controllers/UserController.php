<?php

namespace Controllers;

use Framework\Validate;
use Framework\Response;
use Framework\Session;
use Models\UserModel;
use Framework\Helper;
use Framework\Database;
use Models\UserFileModel;

class UserController
{
    private $userModel;

    public function __construct(Database $db, UserFileModel $userFileModel)
    {
        $this->userModel = new UserModel($db, $userFileModel);
    }

    public function index()
    {
        Helper::loadView('home');
    }

    public function pageRegister()
    {
        Helper::loadView('register');
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
            Helper::loadView('register', [
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
        $this->userModel->createUser($data);

        Helper::loadView('register', [
            'successMsg' => "A regisztráció sikeres volt!"
        ]);
        exit;
    }

    public function pageLogin()
    {
        Helper::loadView('login');
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
            Helper::loadView('login', [
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
        $user = $this->userModel->loginUser($data);

        Session::set('user', [
            'id' => $user['id'],
            'nickname' => $user['nickname'],
        ]);

        Helper::redirect('/profile');
    }

    public function pageProfile()
    {
        $user = $this->userModel->getUserByType('id', Session::get('user')['id']);

        Helper::loadView('profile', ['user' => $user]);
    }

    public function logout()
    {
        // delete all session data
        Session::destroy();

        // delete the session cookie
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        Helper::redirect('/login');
    }

    public function pageEditProfile()
    {
        $user = $this->userModel->getUserByType('id', Session::get('user')['id']);

        Helper::loadView('profile-edit', [
            'user' => [
                'nickname' => $user['nickname'],
                'birthdate' => $user['birthdate'],
            ]
        ]);
    }

    public function editProfile()
    {
        // get the data from the request
        $nickname = $_POST['nickname'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = [];
        // validate the data
        if (!Validate::nickname($nickname)) {
            $errors['nickname'] = "A becenévnek tartalmaznia kell legalább 2 karaktert, valamint csak betűket és számokat tartalmazhat";
        }
        if (!Validate::birthdate($birthdate)) {
            $errors['birthdate'] = "A kor nem lehet kevesebb mint 10, vagy több mint 100";
        }
        if ($password && !Validate::password($password)) {
            $errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        // if there are errors, show the edit profile page with the errors
        if ($errors) {
            http_response_code(Response::$BAD_REQUEST);
            Helper::loadView('profile-edit', [
                'errors' => $errors,
                'user' => [
                    "nickname" => $nickname,
                    "birthdate" => $birthdate,
                ]
            ]);
            exit;
        }

        // update the user
        $data = [
            "id" => Session::get('user')['id'],
            "nickname" => $nickname,
            "birthdate" => $birthdate,
        ];
        if ($password) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $this->userModel->updateUser($data);

        // update the session
        Session::set('user', [
            'id' => Session::get('user')['id'],
            'nickname' => $nickname,
        ]);

        Helper::loadView('profile-edit', [
            'successMsg' => "A profil szerkesztése sikeres volt!",
            'user' => [
                'nickname' => $nickname,
                'birthdate' => $birthdate,
            ]
        ]);
    }
}
