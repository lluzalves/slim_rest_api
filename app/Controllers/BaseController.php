<?php

namespace App\Controllers;

use App\Middleware\TokenAuth;
use Interop\Container\ContainerInterface;

abstract class BaseController
{

    protected $container;

    static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function response($response, $status, $code)
    {
        return $response->withJson(["status" => $status,
            "code" => $code])
            ->withHeader('Content-Type', 'application/json');
    }

    protected function currentUser($request)
    {
        $header = $request->getHeader('Authorization')[0];
        $token = substr($header, strpos($header, '') + 7);
        return TokenAuth::authentication()->getAuth($token);
    }

}