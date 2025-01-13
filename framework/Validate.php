<?php

namespace Framework;

use DateTime;

class Validate
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Validate nickname
    // (2-40 characters, letters and numbers only)
    public static function nickname(string $nickname): bool
    {
        return preg_match('/^[a-zA-Z0-9]{2,40}$/', $nickname) === 1;
    }

    // Validate birthdate
    // (at least 10 years old, less than 100 years old)
    public static function birthdate(string $birthdate): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthdate);
        if (!$date) {
            return false;
        }

        $minAge = 10;
        $maxAge = 100;
        $age = $date->diff(new DateTime())->y;

        return $age >= $minAge && $age < $maxAge;
    }

    // Validate password
    // (6-40 characters, at least one lowercase letter, one uppercase letter, and one digit)
    public static function password(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,40}$/', $password) === 1;
    }
}
