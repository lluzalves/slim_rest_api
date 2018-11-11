<?php

namespace App\Middleware;

use App\Models\User;

class TokenAuth extends BaseAuth
{

    static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function authentication()
    {
        return new TokenAuth();
    }

    public function __invoke($request, $response, $next)
    {

        $auth = $request->getHeader('Authorization');

        if (strpos($auth[0], 'Basic') !== false) {
            return $this->denyAccess($response, 'Invalid request, not allowed', 401);
        }


        if (strpos($auth[0], 'Bearer') !== false) {
            return $this->auth($response, $auth, $request, $next);
        }

        if (empty($auth[0])) {
            return $this->denyAccess($response, 'Missing credentials', 401);
        }

    }

    public function auth($response, $auth, $request, $next)
    {
        $token = $this->getUserToken($auth[0]);

        $user = new User();

        if (!$user->verify($token)) {
            return $this->denyAccess($response, 'Please, check your credentials', 401);
        } else {
            $response = $next($request, $response);
            return $response;
        }
    }

    public function getAuth($token)
    {
        return User::user()->verify($token);
    }

    public function getUserToken($header)
    {
        return substr($header, strpos($header, '') + 7);
    }

}