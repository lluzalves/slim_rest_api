<?php

namespace App\Handlers;

use App\Models\User;

class PasswordHandler
{

    static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verifyPassword($password, $email)
    {
        $user = User::user()->retrieveUserByEmail($email);
        return password_verify($password, $user->password);
    }

    private function resetPassword($email)
    {
        return User::updatePassword($email);
    }

}