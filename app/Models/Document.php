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
        $output['file_url'] = $this->file_url;
        $output['document_uri'] = '/document/' . $this->id;
        $output['is_validated'] = $this->is_validated;
        $output['created_at'] = $this->created_at->toDateTimeString();;
        $output['updated_at'] = $this->updated_at->toDateTimeString();;

        return $output;
    }

}