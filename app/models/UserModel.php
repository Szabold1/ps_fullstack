<?php

declare(strict_types=1);

namespace Models;

use Framework\Database;
use Framework\Response;
use Framework\Helper;

class UserModel
{
    private Database $db;
    private UserFileModel $userFileModel;
    private bool $useDatabase;

    public function __construct(Database $db, UserFileModel $userFileModel, bool $useDatabase = true)
    {
        $this->db = $db;
        $this->userFileModel = $userFileModel;
        $this->useDatabase = $useDatabase;
    }

    // Get user data from database or file, based on 'type' and 'value'
    // e.g. get('email', 'asdf@example.com')
    public function getUserByType(string $type, string $value, bool $useDatabase = true): array|null
    {
        if ($useDatabase) {
            return $this->db->getUserByType($type, $value);
        } else {
            return $this->userFileModel->getUserByType($type, $value);
        }
    }

    public function createUser(array $data): array|null
    {
        // check if user email already exists
        $user = $this->getUserByType('email', $data['email'], $this->useDatabase);
        if ($user) {
            return null;
        }

        // insert user data into database
        $createdUser = $this->db->createUser($data);
        if (!$createdUser) {
            return null;
        }

        // store user data in a file
        $this->userFileModel->createUser($createdUser);

        return $createdUser;
    }

    public function loginUser(array $data): array|null
    {
        // check for email and password
        $user = $this->getUserByType('email', $data['email'], $this->useDatabase);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            return null;
        }

        return $user;
    }

    public function updateUser(array $data): array|null
    {
        // check is user exists
        $user = $this->getUserByType('id', $data['id'], $this->useDatabase);
        if (!$user) {
            return null;
        }

        // update user data in database and file
        $dataToUpdate = [
            'id' => $data['id'],
            'nickname' => $data['nickname'],
            'birthdate' => $data['birthdate'],
            'password_hash' => $data['password_hash'] ?? null,
        ];
        $updatedUser = $this->db->updateUser($dataToUpdate);
        $updatedUserFile = $this->userFileModel->updateUser($dataToUpdate);

        if (!$updatedUser || !$updatedUserFile) {
            return null;
        }

        return $updatedUser;
    }
}
