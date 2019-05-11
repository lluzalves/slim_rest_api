<?php

namespace App\Controllers;

use App\Middleware\TokenAuth;
use App\Models\User;
use Interop\Container\ContainerInterface;
use Slim\Http\UploadedFile;

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

    protected function response($response, $message, $code)
    {
        return $response->withJson(["message" => $message,
            "code" => $code])
            ->withHeader('Content-Type', 'application/json');
    }

    protected function currentUser($request)
    {
        $header = $request->getHeader('Authorization')[0];
        $token = substr($header, strpos($header, '') + 7);
        if (TokenAuth::authentication()->getAuth($token)) {
            return User::user()->getUser($token);
        }
    }

    protected function targetUser($id)
    {
           return User::user()->getUserById($id);
    }

    public function getUserToken($request)
    {
        $auth = $request->getHeader('Authorization');
        return substr($auth[0], strpos($auth[0], '') + 7);
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

}