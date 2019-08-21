<?php

namespace App\Controllers;

use App\Handlers\PasswordHandler;
use App\Models\Document;
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
            $user_notification = NotificationController::createUserNotification(array(
                'receiver_id' => $user->id,
                'body' => "Seja bem vindo ao ifdocs " . $user->name,
                'creator_id' => 1,
                'read_status' => false,
                'type' => 'sucesso'
            ));

            $user_notification->notify($user->email, $user_notification->body);

            $admin_notification = NotificationController::createUserNotification(array(
                'receiver_id' => 1,
                'body' => "Novo cadastro " . $user->email,
                'creator_id' => $user->id,
                'read_status' => false,
                'type' => 'alerta'
            ));

            $admin_notification->notify('ifspdocs@gmail.com', $admin_notification->body);

            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 200]);
        } else {
            return $response->withStatus(400)->withJson('Failed, please try again');
        }
    }

    public function requestUsersByEdict($request, $response, $args)
    {
        if ($args['edict_id'] != null) {
            $documents = Document::where('edict_id', '=', $args['edict_id'])->get();
            if ($documents->count() > 0) {
                foreach ($documents as $_document) {
                    $userIds[] = $_document->user_id;
                }
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $_user) {
                    $payload[] = $_user->output();
                }

                return $response->withStatus(200)->withJson([
                    'message' => 'Success',
                    'code' => 200,
                    'users' => $payload
                ]);
            } else if ($documents->count() <= 0) {
                return $this->response($response, 'No user available', 200);
            } else {
                return $this->response($response, 'Not allowed to access this content', 401);
            }
        }
    }

    public
    function requestUsers($request, $response, $next)
    {
        $user = User::user()->getUser($this->getUserToken($request));
        if ($user[0]->role = 'admin') {
            $users = User::where('role', '=', 'aluno')->get();
            if (count($users) <= 0) {
                return $this->response($response, 'No user available', 200);
            }

            foreach ($users as $_user) {
                $payload[] = $_user->output();
            }

            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 200,
                'users' => $payload
            ]);
        } else {
            return $this->response($response, 'Not allowed to access this content', 401);
        }
    }

    public
    function delete($request, $response, $args)
    {
        if (!empty($args['id'])) {
            if (User::user()->remove($args['id'])) {
                return $this->response($response, 'Deleted successfully', 200);
            } else {
                return $this->response($response, 'Unable to complete request', 400);
            }
        } else {
            return $this->response($response, 'Missing parameter [name], try again', 401);
        }
    }


    public
    function retrieveUserByEmail($request, $response, $args)
    {
        if (!empty($args['email'])) {
            $user = User::user()->retrieveUserByEmail($args['email']);
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

    public
    function retrieveUserByProntuario($request, $response, $args)
    {
        if (!empty($args['prontuario'])) {
            $user = User::user()->retrieveUserByProntuario($args['prontuario']);
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

    public
    function updateInfo($request, $response, $args)
    {
        if (!empty($args['email'])) {
            $user = User::user()->updateEmail($this->getUserToken($request), $request->getParam('email', ''));
            if (!empty($user)) {
                return $this->response($response, 'User updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested user', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [email], try again', 401);
        }
    }

    public
    function recoverCredentials($request, $response, $next)
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