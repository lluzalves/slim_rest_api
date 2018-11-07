<?php

namespace App\Models;


class User extends BaseModel{

    public function auth($apikey){
        $user = User::where('apikey', '=', $apikey)->take(1)->get();
        
        $this->details = $user[0];

        return ($user[0]->exists) ? true : false;
    }
}