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

    private function verifyPassword($password, $username)
    {
        $user = User::user()->retrieveUser($username, $password);
        return $password === $user->password;
    }

}