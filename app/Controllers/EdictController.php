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