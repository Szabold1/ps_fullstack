<?php

namespace Models;

use Framework\Database;
use Framework\Response;
use Framework\Helper;

class UserModel
{
    private $db;
    private $userFileModel;

    public function __construct()
    {
        $config = require Helper::basePath('app/config/db.php');
        $this->db = new Database($config);
        $this->userFileModel = new UserFileModel();
    }

    // Get user data from database or file, based on 'type' and 'value'
    // e.g. get('email', 'asdf@example.com')
    public function getUserByType(string $type, string $value)
    {
        if (rand(0, 1) === 0) {
            return $this->db->query("SELECT * FROM users WHERE $type = :$type", [$type => $value])->fetch();
        } else {
            return $this->userFileModel->getUserByType($type, $value);
        }
    }

    public function createUser(array $data)
    {
        // check if user email already exists
        $user = $this->getUserByType('email', $data['email']);
        if ($user) {
            http_response_code(Response::$CONFLICT);
            Helper::loadView('register', [
                'errors' => ['email' => 'Az email cím már foglalt'],
                'user' => [
                    'email' => $data['email'],
                    'nickname' => $data['nickname'],
                    'birthdate' => $data['birthdate']
                ]
            ]);
            exit;
        }

        // insert user data into database
        $this->db->query("INSERT INTO users (email, nickname, birthdate, password_hash) VALUES (:email, :nickname, :birthdate, :password_hash)", $data);

        // add the id of the user to data
        $data['id'] = $this->db->connection->lastInsertId();

        // store user data in a file
        $this->userFileModel->createUser($data);
    }

    public function loginUser(array $data)
    {
        // check for email and password
        $user = $this->getUserByType('email', $data['email']);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            http_response_code(Response::$UNAUTHORIZED);
            Helper::loadView('login', [
                'errors' => ['general' => 'Helytelen email cím vagy jelszó'],
                'user' => [
                    'email' => $data['email']
                ]
            ]);
            exit;
        }

        return $user;
    }

    public function updateUser(array $data)
    {
        // check is user exists
        $user = $this->getUserByType('id', $data['id']);
        if (!$user) {
            http_response_code(Response::$UNAUTHORIZED);
            Helper::loadView('profile', [
                'errors' => ['general' => 'Nem létezik ilyen felhasználó'],
            ]);
            exit;
        }

        // update user data in database and file
        $dataToUpdate = "nickname = :nickname, birthdate = :birthdate";
        if (isset($data['password_hash'])) {
            $dataToUpdate .= ", password_hash = :password_hash";
        }
        $this->db->query("UPDATE users SET {$dataToUpdate} WHERE id = :id", $data);
        $this->userFileModel->updateUser($data);
    }
}
