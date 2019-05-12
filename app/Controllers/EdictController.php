<?php

namespace App\Controllers;

use App\Models\Edict;
use Illuminate\Database\QueryException;

class EdictController extends BaseController
{

    public function allEdicts($request, $response)
    {
        $currentUser = $this->currentUser($request);

        $role = $currentUser[0]->role;

        $edicts = Edict::allForRole($role);
        if (count($edicts) <= 0) {
            return $this->response($response, 'No edicts having been created yet', 200);
        }

        foreach ($edicts as $edict) {
            $payload[] = $edict;
        }

        return $response->withStatus(200)->withJson([
            'message' => 'Success',
            'code' => 200,
            'edicts' => $payload
        ]);
    }

    public function allEdictsType($request, $response)
    {
        $currentUser = $this->currentUser($request);

        $role = $currentUser[0]->role;

        $edicts = Edict::allForType($role);
        if (count($edicts) <= 0) {
            return $this->response($response, 'No edicts having been created yet', 200);
        }

        foreach ($edicts as $edict) {
            $payload[] = $edict;
        }

        return $response->withStatus(200)->withJson([
            'message' => 'Success',
            'code' => 200,
            'edicts' => $payload
        ]);
    }

    public function edictDetails($request, $response, $args)
    {
        if (!empty($args['edict_id'])) {
            $edict = Edict::edict()->retrieveById($args['edict_id']);
            if ($edict[0]->exists) {
                $payload = $edict;
                return $response->withStatus(200)->withJson([
                    'message' => 'Success',
                    'code' => 200,
                    'edict' => $payload
                ]);
            } else {
                return $this->response($response, 'Unable to find requested edict', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [id], try again', 401);
        }
    }

    public function create($request, $response)
    {
        try {
            $edict = Edict::edict()->create($request);
        } catch (QueryException $query_exception) {
            return $response->withStatus(409)->withJson($query_exception->getMessage());
        }

        if ($edict != null) {
            return $response->withStatus(200)
                ->withJson([
                    'message' => 'Success',
                    'code' => 200,
                    'edict' => array($edict->output())
                ]);
        }
    }
}