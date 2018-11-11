<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends BaseController
{

    public function create($request, $response)
    {

        $user = new User();
        $user->username = $request->getParsedBodyParam('username', '');
        $user->email = $request->getParsedBodyParam('email', '');
        $user->password = password_hash($request->getParsedBodyParam('password', ''), PASSWORD_BCRYPT);
        $user->token = $token = bin2hex(random_bytes(64));
        $user->token_expiration = date('Y-m-d');
        $user->role =
            $user->save();

        if ($user->id) {
            return $response->withStatus(200)->withJson('User created, successfully');
        } else {
            return $response->withStatus(400)->withJson('Failed, please try again');
        }
    }

    public function delete($request, $response, $args)
    {

        if (User::user()->remove($args['id'])) {
            return $this->response($response, 'Deleted successfully', 204);
        }
        return $this->response($response, 'Unable to complete request', 400);

    }


    public function retrieve($request, $response, $args)
    {
        $user = User::where('token', '=', $args['token'])
            ->take(1)
            ->get();

        if ($user[0]->exists()) {
            $payload = $user[0]->output();
            return $response->withStatus(200)->withJson($payload);
        } else {
            return $this->response($response, 'Failed', 401);
        }
    }

    public function update($request, $response, $args)
    {
        $user = User::where('token', '=', $args['token']);

        $user->username = $request->getParsedBodyParam('username', '');
        $user->email = $request->getParsedBodyParam('email', '');
        $user->password = password_hash($request->getParsedBodyParam('password', ''), PASSWORD_BCRYPT);

        $user->save();

        if ($user->id) {
            $payload = $user->output();
            return $response->withStatus(201)->withJson($payload);
        } else {
            return $response->withStatus(400);
        }
    }

}