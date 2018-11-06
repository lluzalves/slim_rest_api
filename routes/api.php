<?php

use App\Models\Document;

$app->get('/documents',function($request,$response,$args){
     $_document = new Document();
     $documents = $_document->all();

     $payload = [];
     foreach($documents as $_doc){
        $payload[$_doc->id] = ['description' => $_doc->description,
                               'user_id' => $_doc->user_id,
                               'file_url' => $_doc->file_url,
                               'created_at' => $_doc->created_at
                            ];
     }
     return $response->withStatus(200)->withJson($payload);
});