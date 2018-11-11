<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 11/11/2018
 * Time: 13:53
 */

namespace App\Middleware;

abstract class BaseAuth
{

    public function denyAccess($response, $message, $status)
    {
        return $response->withJson(["message" => $message,
            "status" => $status])
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public function allowAccess($response, $message, $status)
    {
        return $response->withJson(["message" => $message,
            "status" => $status])
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

}