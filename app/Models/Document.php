<?php

namespace App\Models;

class Document extends BaseModel{

    public function output(){
       
        $output = [];
        $output['description'] = $this->description;
        $output['user_id'] = $this->user_id;
        $output['file_url'] = $this->file_url;
        $output['document_uri'] = '/document/' .$this->id;
        $output['created_at'] = $this->created_at;
        $output['updated_at'] = $this->updated_at;

        return $output;
    }
    
}