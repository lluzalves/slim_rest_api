<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends BaseController
{

    public function create($request, $response, $args)
    {
        $user = User::user()->create($request);
        if (!empty($user)) {
            return $response->withStatus(200)->withJson($user->tokenOutput());
        } else {
            return $response->withStatus(400)->withJson('Failed, please try again');
        }
    }

    public function delete($request, $response, $args)
    {
        if (!empty($args['id'])) {
            if (User::user()->remove($args['id'])) {
                return $this->response($response, 'Deleted successfully', 204);
            } else {
                return $this->response($response, 'Unable to complete request', 400);
            }
        } else {
            return $this->response($response, 'Missing parameter [username], try again', 401);
        }
    }


    public function retrieve($request, $response, $args)
    {
        if (!empty($args['username'])) {
            $user = User::user()->retrieveUser($args['username']);
            if ($user->exists) {
                $payload = $user->output();
                return $response->withStatus(200)->withJson($payload);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [username], try again', 401);
        }
    }

    public function updateInfo($request, $response, $args)
    {
        if (!empty($args['username'])) {
            $user = User::user()->updateInfo($args['username'],$request->getParam('username', ''), $request->getParam('email', ''));
            if (!empty($user)) {
                return $this->response($response, 'User updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [username], try again', 401);
        }
    }

}