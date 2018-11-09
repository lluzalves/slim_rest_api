<?php

namespace App\Models;

use DateTime;

class User extends BaseModel
{

    protected $currentUser;
    protected $fillable = ['username', 'password'];

    static function user()
    {
        return new User();
    }

    public function verify($token)
    {
        $user = User::where('token', '=', $token)
            ->take(1)
            ->get();

        $this->currentUser = $user[0];

        return ($user[0]->exists && $this->isUserTokenStillValid($this->currentUser)) ? true : false;
    }


    private function isUserTokenStillValid($currentUser)
    {
        $date = new DateTime($currentUser->token_expiration);

        return $date->format('Y-m-d') >= date('Y-m-d');
    }

    public function currentUser($token)
    {
        $isAuth = $this->verify($token);

        if ($isAuth) {
            return $this->currentUser;
        }
    }

    public function retrieveUser($username, $password)
    {
        $user = User::where('username', '=', $username)
            ->where('password', '=', $password)
            ->take(1)
            ->get();

        $this->currentUser = $user[0];
        if ($user[0]->exists) return $user[0];
    }
}