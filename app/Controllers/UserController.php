<?php

namespace App\Controllers;

use App\Handlers\PasswordHandler;
use App\Models\User;
use Illuminate\Database\QueryException;

class UserController extends BaseController
{

    public function create($request, $response, $args)
    {
        try {
            $user = User::user()->create($request);
        } catch (QueryException $query_exception) {
            return $response->withStatus(409)->withJson($query_exception->getMessage());
        }
        if (!is_null($user)) {
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 200]);
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
            return $this->response($response, 'Missing parameter [name], try again', 401);
        }
    }


    public function retrieve($request, $response, $args)
    {
        if (!empty($args['email'])) {
            $user = User::user()->retrieveUser($args['email']);
            if ($user->exists) {
                $payload = $user->output();
                return $response->withStatus(200)->withJson([
                    'message' => 'Success',
                    'code' => 200,
                    'user' => $payload
                ]);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [name], try again', 401);
        }
    }

    public function updateInfo($request, $response, $args)
    {
        if (!empty($args['email'])) {
            $user = User::user()->updateEmail($this->getUserToken($request), $request->getParam('email', ''));
            if (!empty($user)) {
                return $this->response($response, 'User updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [name], try again', 401);
        }
    }

    public function recover($request, $response, $next)
    {
        if (!empty($args['email'])) {
            $user = PasswordHandler::recover()->updateEmail($this->getUserToken($request), $request->getParam('email', ''));
            if (!empty($user)) {
                return $this->response($response, 'User updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [name], try again', 401);
        }
    }

    public function recoverCredentials($request, $response, $next)
    {
        $email = $request->getParsedBodyParam('email', '');
        if (PasswordHandler::resetPassword($email)) {
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'email' => $email,
                'code' => 200,
            ]);
        } else {
            return $response->withStatus(200)->withJson([
                'message' => 'Failure',
                'email' => $email,
                'code' => 401,
            ]);
        }
    }

}