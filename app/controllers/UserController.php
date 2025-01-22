<?php

declare(strict_types=1);

namespace Controllers;

use Framework\Response;
use Framework\Session;
use Models\UserModel;
use Framework\Helper;
use Framework\Database;
use Models\UserFileModel;
use Framework\Form;

class UserController
{
    private $userModel;
    private $useDatabase;
    private $form;

    public function __construct(Database $db, UserFileModel $userFileModel, bool $useDatabse = true)
    {
        $this->useDatabase = $useDatabse;
        $this->userModel = new UserModel($db, $userFileModel, $this->useDatabase);
        $this->form = new Form();
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

        // if validation fails...
        if (!$this->form->validateRegistration([
            'email' => $email,
            'nickname' => $nickname,
            'birthdate' => $birthdate,
            'password' => $password
        ])) {
            // get the errors and show the register page with the errors
            $errors = $this->form->getErrors();
            http_response_code(Response::$BAD_REQUEST);
            Helper::loadView('register', [
                'errors' => $errors,
                'user' => [
                    "email" => $email,
                    "nickname" => $nickname,
                    "birthdate" => $birthdate,
                ]
            ]);
        }

        // create the user
        $newUser = $this->userModel->createUser([
            "email" => $email,
            "nickname" => $nickname,
            "birthdate" => $birthdate,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
        if (!$newUser) {
            http_response_code(Response::$CONFLICT);
            Helper::loadView('register', [
                'errors' => ['email' => 'Az email cím már foglalt'],
                'user' => [
                    'email' => $email,
                    'nickname' => $nickname,
                    'birthdate' => $birthdate
                ]
            ]);
        }

        Helper::loadView('register', [
            'successMsg' => "A regisztráció sikeres volt!"
        ]);
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

        // if validation fails...
        if (!$this->form->validateLogin([
            'email' => $email,
            'password' => $password
        ])) {
            // get the errors and show the login page with the errors
            $errors = $this->form->getErrors();
            http_response_code(Response::$BAD_REQUEST);
            Helper::loadView('login', [
                'errors' => $errors,
                'user' => [
                    "email" => $email,
                ]
            ]);
        }

        // login the user
        $data = [
            "email" => $email,
            "password" => $password
        ];
        $user = $this->userModel->loginUser($data);
        if (!$user) {
            http_response_code(Response::$UNAUTHORIZED);
            Helper::loadView('login', [
                'errors' => ['general' => 'Helytelen email cím vagy jelszó'],
                'user' => [
                    'email' => $data['email']
                ]
            ]);
        }

        Session::set('user', [
            'id' => (string)$user['id'],
            'nickname' => $user['nickname'],
        ]);

        Helper::redirect('/profile');
    }

    public function pageProfile()
    {
        $user = $this->userModel->getUserByType('id', Session::get('user')['id'], $this->useDatabase);

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
        $user = $this->userModel->getUserByType('id', Session::get('user')['id'], $this->useDatabase);

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

        // if validation fails...
        if (!$this->form->validateEditProfile([
            'nickname' => $nickname,
            'birthdate' => $birthdate,
            'password' => $password
        ])) {
            // get the errors and show the edit profile page with the errors
            $errors = $this->form->getErrors();
            http_response_code(Response::$BAD_REQUEST);
            Helper::loadView('profile-edit', [
                'errors' => $errors,
                'user' => [
                    "nickname" => $nickname,
                    "birthdate" => $birthdate,
                ]
            ]);
        }

        // update the user
        $data = [
            "id" => Session::get('user')['id'],
            "nickname" => $nickname,
            "birthdate" => $birthdate,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT) ?? null,
        ];
        $user = $this->userModel->updateUser($data);
        if (!$user) {
            http_response_code(Response::$UNAUTHORIZED);
            Helper::loadView('profile', [
                'errors' => ['general' => 'Nem létezik ilyen felhasználó'],
            ]);
        }

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
