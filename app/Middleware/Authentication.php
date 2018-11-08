<?php

namespace App\Middleware;

use App\Models\User;

class Authentication{

    public function __invoke($request, $response, $next){

        $auth = $request->getHeader('Authorization');
        
        if(empty($auth[0])){
            return $response->withStatus(401);
        }
        
        $_apikey = $auth[0];
        $apikey = substr($_apikey, strpos($_apikey,'')+7);
                    
        $user = new User();
        
        if(!$user->auth($apikey)){
            return $response->withStatus(401);
        }

        $response = $next($request,$response);
        
        return $response;  
    }
}