<?php

namespace Models;

use Framework\Helper;

class UserFileModel
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        // create directory if it doesn't exist
        $directory = dirname($this->filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // create file if it doesn't exist
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, '[]');
            chmod($this->filePath, 0644);
        }
    }

    private function getAllUsers()
    {
        if (file_exists($this->filePath)) {
            $fileContent = file_get_contents($this->filePath);
            return json_decode($fileContent, true) ?? [];
        }

        return [];
    }

    public function getUserByType(string $type, string $value)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if (isset($user[$type]) && $user[$type] === $value) {
                return $user;
            }
        }

        return null;
    }

    public function createUser(array $data)
    {
        $users = $this->getAllUsers();

        $users[] = $data;
        file_put_contents($this->filePath, json_encode($users));
    }

    public function updateUser(array $data)
    {
        $user = $this->getUserByType('id', $data['id']);

        if ($user) {
            // update user data
            $user['nickname'] = $data['nickname'];
            $user['birthdate'] = $data['birthdate'];
            if (isset($data['password_hash'])) {
                $user['password_hash'] = $data['password_hash'];
            }

            // get all users from file
            $users = $this->getAllUsers();

            // update the user in the data read from the file
            foreach ($users as $index => $existingUser) {
                if ($existingUser['id'] === $user['id']) {
                    $users[$index] = array_merge($existingUser, $user);
                    break;
                }
            }

            // write the updated data back to the file
            file_put_contents($this->filePath, json_encode($users));
        }
    }
}
