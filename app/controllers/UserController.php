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
            Session::setFlash(Session::ERRORS, $this->form->getErrors());
            Session::setFlash(Session::USER, [
                'email' => $email,
                'nickname' => $nickname,
                'birthdate' => $birthdate,
            ]);

            http_response_code(Response::$BAD_REQUEST);
            Helper::redirect('/register');
        }

        // create the user
        $newUser = $this->userModel->createUser([
            "email" => $email,
            "nickname" => $nickname,
            "birthdate" => $birthdate,
            "password_hash" => password_hash($password, PASSWORD_DEFAULT)
        ]);
        if (!$newUser) {
            Session::setFlash('errors', ['email' => 'Ez az email cím már foglalt']);
            Session::setFlash(Session::USER, [
                'email' => $email,
                'nickname' => $nickname,
                'birthdate' => $birthdate,
            ]);

            http_response_code(Response::$CONFLICT);
            Helper::redirect('/register');
        }

        Session::setFlash('success', 'A regisztráció sikeres volt!');
        Helper::redirect('/register');
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
            Session::setFlash(Session::ERRORS, $this->form->getErrors());
            Session::setFlash(Session::USER, ['email' => $email]);
            http_response_code(Response::$BAD_REQUEST);
            Helper::redirect('/login');
        }

        // login the user
        $user = $this->userModel->loginUser([
            "email" => $email,
            "password" => $password
        ]);
        if (!$user) {
            Session::setFlash(Session::ERRORS, ['general' => 'Helytelen email cím vagy jelszó']);
            Session::setFlash(Session::USER, ['email' => $email]);
            http_response_code(Response::$UNAUTHORIZED);
            Helper::redirect('/login');
        }

        Session::set(Session::USER, [
            'id' => (string)$user['id'],
            'nickname' => $user['nickname'],
        ]);

        Helper::redirect('/profile');
    }

    public function pageProfile()
    {
        $user = $this->userModel->getUserByType('id', Session::get(Session::USER)['id'], $this->useDatabase);

        Session::setFlash(Session::USER, ['nickname' => $user['nickname']]);
        Helper::loadView('profile');
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
        $user = $this->userModel->getUserByType('id', Session::get(Session::USER)['id'], $this->useDatabase);

        Session::setFlash(Session::USER, [
            'nickname' => $user['nickname'],
            'birthdate' => $user['birthdate'],
        ]);
        Helper::loadView('profile-edit');
    }

    public function editProfile()
    {
        // get the data from the request
        $nickname = $_POST['nickname'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $password = $_POST['password'] ?? null;

        // if validation fails...
        if (!$this->form->validateEditProfile([
            'nickname' => $nickname,
            'birthdate' => $birthdate,
            'password' => $password
        ])) {
            Session::setFlash(Session::ERRORS, $this->form->getErrors());
            Session::setFlash(Session::USER, [
                'nickname' => $nickname,
                'birthdate' => $birthdate,
            ]);
            http_response_code(Response::$BAD_REQUEST);
            Helper::redirect('/profile/edit');
        }

        // update the user
        $hashedPassword = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
        $user = $this->userModel->updateUser([
            "id" => Session::get(Session::USER)['id'],
            "nickname" => $nickname,
            "birthdate" => $birthdate,
            "password_hash" => $hashedPassword
        ]);
        if (!$user) {
            Session::setFlash(Session::ERRORS, ['general' => 'Nem létezik ilyen felhasználó']);
            http_response_code(Response::$UNAUTHORIZED);
            Helper::redirect('/profile/edit');
        }

        // update the session
        Session::set(Session::USER, [
            'id' => Session::get(Session::USER)['id'],
            'nickname' => $nickname,
        ]);

        Session::setFlash(Session::SUCCESS, 'A profil szerkesztése sikeres volt!');
        Session::setFlash(Session::USER, [
            'nickname' => $nickname,
            'birthdate' => $birthdate,
        ]);
        Helper::redirect('/profile/edit');
    }
}
