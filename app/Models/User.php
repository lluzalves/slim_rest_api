<?php

namespace App\Models;

use DateTime;

class User extends BaseModel
{

    protected $currentUser;

    static function user()
    {
        return new User();
    }

    public function auth($token)
    {
        $user = User::where('token', '=', $token)->take(1)->get();

        $this->currentUser = $user[0];

        return ($user[0]->exists && $this->isUserTokenStillValid($this->currentUser)) ? true : false;
    }

    public function isUserTokenStillValid($currentUser)
    {
        $date = new DateTime($currentUser->token_expiration);

        return $date->format('Y-m-d') >= date('Y-m-d');
    }

    public function currentUser($token)
    {

        $isAuth = $this->auth($token);

        if ($isAuth) {
            return $this->currentUser;
        }
    }
}