<?php

namespace App\Models;

use DateTime;

class User extends BaseModel
{

    public $timestamps = false;
    protected $currentUser;
    protected $fillable = ['name', 'password'];

    static function user()
    {
        return new User();
    }

    public function output()
    {
        $output = [];
        $output['email'] = $this->email;
        $output['token_expiration'] = $this->token_expiration;
        $output['name'] = $this->name;
        $output['user_uri'] = '/users/' . $this->id;
        $output['created_at'] = $this->created_at->toDateTimeString();;
        $output['updated_at'] = $this->updated_at->toDateTimeString();;

        return $output;
    }


    public function create($request)
    {
        $user = new User();

        $user->name = $request->getParsedBodyParam('name', '');
        $user->email = $request->getParsedBodyParam('email', '');
        $user->password = password_hash($request->getParsedBodyParam('password', ''), PASSWORD_BCRYPT);
        $user->token = $token = bin2hex(random_bytes(64));
        $user->token_expiration = date('Y-m-d+23:59:59');

        $user->save();

        return $user;
    }

    public function tokenOutput()
    {
        $output = [];
        $output['email'] = $this->email;
        $output['token'] = $this->token;

        return $output;
    }

    public function verify($token)
    {
        $user = User::where('token', '=', $token)->take(1)->get();

        $this->currentUser = $user[0];

        if (empty($this->currentUser)) {
            return false;
        }

        return ($user[0]->exists()) ? true : false;
    }


    private function isTokenExpired($currentUser)
    {
        $date = new DateTime($currentUser->token_expiration);

        return $date->format('Y-m-d') < date('Y-m-d');
    }


    public function retrieveUser($email)
    {
        $user = User::where('email', '=', $email)->take(1)->get();

        $this->currentUser = $user[0];

        return $this->currentUser;
    }

    public function remove($id)
    {
        $user = User::where('id', '=', $id)->take(1)->get();

        if ($user[0]->exists()) {
            return $user->delete();
        }
        return $user[0]->exists();
    }

    public function generateToken($email)
    {
        $user = User::where('email', '=', $email)->take(1)->get();

        $user[0]->token = bin2hex(random_bytes(64));

        if ($user[0]->save()) {
            return $user[0]->token;
        }

        return empty(true);
    }

    public function getUser($token)
    {
        return User::where('token', '=', $token)->take(1)->get();
    }

    public function updateEmail($token, $newEmail)
    {
        $user = User::where('token', '=', $token)->take(1)->get();

        if (!empty($user[0])) {
            $user[0]->email = $newEmail;
            return $user[0]->save();
        } else {
            return false;
        }

    }

    public function updatePassword($token, $password)
    {
        $user = User::where('token', '=', $token)->take(1)->get();

        $user->password = password_hash($password, PASSWORD_BCRYPT);

        return $user->save();
    }

    public function isAdmin($email)
    {
        $user = User::where('email', '=', $email)->take(1)->get();

        return $user[0]->role === 'admin';
    }
}