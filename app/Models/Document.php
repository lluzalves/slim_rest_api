<?php

namespace App\Models;


class Document extends BaseModel
{

    public function output()
    {

        $output = [];
        $output['id'] = $this->id;
        $output['description'] = $this->description;
        $output['user_id'] = $this->user_id;
        $output['document_uri'] = '/document/' . $this->id;
        $output['is_validated'] = $this->is_validated;
        $output['notification'] = $this->notification;
        $output['type'] = $this->type;
        $output['created_at'] = $this->created_at->toDateTimeString();;
        $output['updated_at'] = $this->updated_at->toDateTimeString();;

        return $output;
    }

    public function fileOutput()
    {

        $output = [];
        $output['id'] = $this->id;
        $output['user_id'] = $this->user_id;
        $output['file_url'] = $this->file_url;

        return $output;
    }

    public function retrieveDocument($document_id)
    {
        $document = Document::where('id', '=', $document_id)->take(1)->get();

        return $this->$document[0];
    }

}