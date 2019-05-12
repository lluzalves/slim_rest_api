<?php


namespace App\Models;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Carbon;

class Edict extends BaseModel
{

    static function edict()
    {
        return new Edict();
    }

    public function output()
    {

        $output = [];
        $output['id'] = $this->id;
        $output['title'] = $this->title;
        $output['roles'] = $this->roles;
        $output['starts_at'] = $this->starts_at;
        $output['end_at'] = $this->end_at;
        $output['is_available'] = $this->is_available;
        $output['type'] = $this->type;
        $output['notification'] = $this->notification;
        $output['created_at'] = $this->created_at->toDateTimeString();
        $output['updated_at'] = $this->updated_at->toDateTimeString();

        return $output;
    }

    public function allForRole($role)
    {
        $edicts = DB::table('edicts')->where([
            ['roles', '=', $role],
            ['end_at', '>=', Carbon::today()->toDateString()]
        ])->get();

        return $edicts;
    }

    public function allForType($type)
    {
        $edicts = DB::table('edicts')->where([
            ['type', '=', $type],
            ['end_at', '>=', Carbon::today()->toDateString()]
        ])->get();

        return $edicts;
    }

    public function create($request)
    {
        $edict = new Edict();

        $edict->description = $request->getParsedBodyParam('description', '');
        $edict->title = $request->getParsedBodyParam('title', '');
        $edict->roles = $request->getParsedBodyParam('roles', 'admin');
        $edict->starts_at = $request->getParsedBodyParam('starts_at', '');
        $edict->created_by = $request->getParsedBodyParam('created_by', '');
        $edict->end_at = $request->getParsedBodyParam('end_at', '');
        $edict->type = $request->getParsedBodyParam('type', '');
        $edict->is_available = 0;
        $edict->notification = "Novo edital - " . $edict->description;

        if (empty($edict->description) or empty($edict->title) or empty($edict->roles) or empty($edict->starts_at) or empty($edict->end_at)) {
            return null;
        }

        $edict->save();

        return $edict;
    }

    public function retrieveById($id)
    {
        $edict = Edict::where('id', '=', $id)->take(1)->get();
        return $edict;
    }

}