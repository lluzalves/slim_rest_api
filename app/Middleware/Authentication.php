<?php

namespace App\Middleware;

use App\Handlers\PasswordHandler;
use App\Models\User;
use Slim\Http\Response;

class Authentication
{

    static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __invoke($request, $response, $next)
    {

        $auth = $request->getHeader('Authorization');

        if (strpos($auth[0], 'Basic') !== false) {
            return $this->login($request, $response, $next);
        }

        return $this->auth($response, $auth, $request, $next);
    }

    public function auth($response, $auth, $request, $next)
    {
        if (empty($auth[0])) {
            return $this->denyAccess($response, 'Missing credentials', 412);
        }

        $token = $this->getUserToken($auth[0]);

        $user = new User();

        if (!$user->verify($token)) {
            return $this->denyAccess($response, 'Please, check your credentials', 401);
        } else {
            $response = $next($request, $response);
            return $response;
        }
    }

    public function login($request, $response, $next)
    {
        if (empty($request->getServerParams()['PHP_AUTH_USER']) && empty($request->getServerParams()['PHP_AUTH_PW'])) {
            $response = $next($request, $response);
            return $this->denyAccess($response, 'Missing credentials', 412);
        }

        $user = $request->getServerParams()['PHP_AUTH_USER'];
        $password = $request->getServerParams()['PHP_AUTH_PW'];

        if (PasswordHandler::verifyPassword($password, $user)) {
            $response = $next($request, $response);
            return $this->allowAccess($response, 'Authenticated');
        } else {
            $response = $next($request, $response);
            return $this->denyAccess($response, 'Please, check your credentials', 401);
        }
    }

    public function getUserToken($header)
    {
        return substr($header, strpos($header, '') + 7);
    }

    public function denyAccess($response, $message, $status)
    {
        return $response->withJson(["message" => $message,
            "status" => $status])
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public function allowAccess($response, $message)
    {
        return $response->withJson(["status" => $message,
            "code" => 200])
            ->withStatus[200]
            ->withHeader('Content-Type', 'application/json');
    }

}