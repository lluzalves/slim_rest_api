<?php

namespace App\Controllers;

use App\Models\Document;
use Slim\Http\UploadedFile;

class DocumentController extends BaseController {

    protected $filename;
    
     public function all($_request, $response,$args){
        $_document = new Document();
        $documents = $_document->all();
    
        $payload = [];
        foreach($documents as $_doc){
           $payload[$_doc->id] = $_doc->output();
        }
        return $response->withStatus(200)->withJson($payload);
     }

    public function add($request, $response,$args){

        $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'documentos';

        $uploadedFiles = $request->getUploadedFiles();

        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename . '<br/>');
        }

        $document = new Document();
        $document->description = $request->getParsedBodyParam('description', '');
        $document->user_id = 1;
        $document->file_url = $directory . DIRECTORY_SEPARATOR . $filename;
        $document->save();
    
        if($document->id){
            $payload = $document->output();
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

    public function update($request, $response, $next) {
        $_isvalidated = $request->getParsedBodyParam('is_validated');
        
        $document = Document::find($args['document_id']);
        $document->is_validated = $_isvalidated;
        $document->save();

        if($document->id){
            $payload = $document->output();
            return $response->withStatus(201)->withJson($payload);
        }else{
            return $response->withStatus(400);
        }
    }

    public function deleteAll($request, $response,$args){
        $document = Document::find($args['document_id']);
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile){
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    
    $basename = bin2hex(random_bytes(8)); 
    
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

}