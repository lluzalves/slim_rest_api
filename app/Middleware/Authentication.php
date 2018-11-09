<?php

namespace App\Middleware;

use App\Models\User;

class Authentication
{

    public function __invoke($request, $response, $next)
    {

        $auth = $request->getHeader('Authorization');

        if (empty($auth[0])) {
            return $this->denyAccess($response, 'Missing credentials');
        }

        $token = $this->getUserToken($auth[0]);

        $user = new User();
        if (!$user->auth($token)) {
            return $this->denyAccess($response, 'Invalid credentials');
        }

        $response = $next($request, $response);

        return $response;
    }

    public function denyAccess($response, $status)
    {
        return $response->withJson(["status" => $status,
            "code" => 401])
            ->withHeader('Content-Type', 'application/json');
    }

    public function getUserToken($header)
    {
        return substr($header, strpos($header, '') + 7);
    }

}