<?php

namespace Framework;

class Form
{
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validateRegistration(array $data): bool
    {
        if (!Validate::email($data['email'] ?? '')) {
            $this->errors['email'] = "Az email cím formátuma nem megfelelő";
        }
        if (!Validate::nickname($data['nickname'] ?? '')) {
            $this->errors['nickname'] = "A becenévnek tartalmaznia kell legalább 2 karaktert, valamint csak betűket és számokat tartalmazhat";
        }
        if (!Validate::birthdate($data['birthdate'] ?? '')) {
            $this->errors['birthdate'] = "A kor nem lehet kevesebb mint 10, vagy több mint 100";
        }
        if (!Validate::password($data['password'] ?? '')) {
            $this->errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        return empty($this->errors);
    }

    public function validateLogin(array $data): bool
    {
        if (!Validate::email($data['email'] ?? '')) {
            $this->errors['email'] = "Az email cím formátuma nem megfelelő";
        }
        if (!Validate::password($data['password'] ?? '')) {
            $this->errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        return empty($this->errors);
    }

    public function validateEditProfile(array $data): bool
    {
        if (!Validate::nickname($data['nickname'] ?? '')) {
            $this->errors['nickname'] = "A becenévnek tartalmaznia kell legalább 2 karaktert, valamint csak betűket és számokat tartalmazhat";
        }
        if (!Validate::birthdate($data['birthdate'] ?? '')) {
            $this->errors['birthdate'] = "A kor nem lehet kevesebb mint 10, vagy több mint 100";
        }
        if ($data['password'] && !Validate::password($data['password'] ?? '')) {
            $this->errors['password'] = "A jelszónak tartalmaznia kell legalább egy kisbetűt, egy nagybetűt és egy számot, valamint legalább 6 karakter hosszúnak kell lennie";
        }

        return empty($this->errors);
    }
}
