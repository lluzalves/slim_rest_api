<?php

namespace App\Controllers;

use App\Models\Document;

class DocumentController extends BaseController {
    
     public function all($_request, $response,$args){
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
     }


    public function add($request, $response,$args){
        $_document = $request->getParsedBodyParam('description', '');

        $document = new Document();
        $document->description = $_document;
        $document->user_id = 1;
        $document->file_url = '';
        $document->save();
    
        if($document->id){
            $payload = ['document_id' => $document->id,
                        'document_uri' => '/documents/' . $document->id];
            return $response->withStatus(200)->withJson($payload);
        }else{
            return $response->withStatus(400);
        }
    }


    public function delete($request, $response,$args){
        $document = Document::find($args['document_id']);
        $document->delete();
   
        if($document->exists){
            return $response->withStatus(400);
        }else{
            return $response->withStatus(204);
        }
    }

    public function deleteAll($request, $response,$args){
        $document = Document::find($args['document_id']);

        $document = new Document();
        $document->description = $_document;
        $document->user_id = 1;
        $document->file_url = '';
        $document->save();
    
        if($document->id){
            $payload = ['document_id' => $document->id,
                        'document_uri' => '/documents/' . $document->id];
            return $response->withStatus(200)->withJson($payload);
        }else{
            return $response->withStatus(400);
        }
    }
}