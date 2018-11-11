<?php

namespace App\Middleware;
use App\Handlers\PasswordHandler;
use App\Models\User;

class BasicAuth extends BaseAuth
{

    static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function authentication()
    {
        return new BasicAuth();
    }

    public function __invoke($request, $response, $next)
    {

        $auth = $request->getHeader('Authorization');

        if (strpos($auth[0], 'Basic') !== false) {
            return $this->login($request, $response, $next);
        }

        return $this->denyAccess($response,'Invalid request',401);

    }

    public function login($request, $response, $next)
    {
        if (empty($request->getServerParams()['PHP_AUTH_USER']) && empty($request->getServerParams()['PHP_AUTH_PW'])) {
            $response = $next($request, $response);
            return $this->denyAccess($response, 'Missing credentials', 401);
        }

        $username = $request->getServerParams()['PHP_AUTH_USER'];
        $password = $request->getServerParams()['PHP_AUTH_PW'];

        if (PasswordHandler::verifyPassword($password, $username)) {
            $response = $next($request, $response);
            User::updateToken($username);
            return $this->allowAccess($response, 'Authenticated',200);
        } else {
            $response = $next($request, $response);
            return $this->denyAccess($response, 'Please, check your credentials', 401);
        }
    }

}