<?php

namespace App\Controllers;

use App\Models\Document;
use Slim\Http\UploadedFile;

class DocumentController extends BaseController
{

    protected $filename;
    protected $payload = [];
    protected $currentUser;

    public function all($request, $response)
    {
        $currentUser = $this->currentUser($request);

        $documents = Document::where('user_id', '=', $currentUser[0]->id)->get();

        if (count($documents) <= 0) {
            return $this->response($response, 'No documents available for this user', 204);
        }

        foreach ($documents as $_document) {
            $payload[] = $_document->output();
        }

        return $response->withStatus(200)->withJson([
            'message' => 'Success',
            'code' => 204,
            'documents' => $payload
        ]);
    }

    public function add($request, $response)
    {

        $currentUser = $this->currentUser($request);

        $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'raw';
        $uploadedFiles = $request->getUploadedFiles();

        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename . '<br/>');
        }

        $document = new Document();
        $document->description = $request->getParsedBodyParam('description', '');
        $document->user_id = $currentUser->id;
        $document->is_validated = false;
        $document->file_url = $directory . DIRECTORY_SEPARATOR . $filename;
        $document->save();

        if ($document->id) {
            $payload[] = $document->output();
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 204,
                'documents' => $payload
            ]);
        } else {
            return $response->withStatus(400);
        }
    }

    public function delete($request, $response, $args)
    {
        $document = Document::where('document_id', '=', $args['document_id'])
            ->where('user_id', '=', $this->getCurrentUserId($request)->id);
        $document->delete();

        if ($document->exists) {
            return $this->response($response, 'Unable to complete request', 400);
        } else {
            return $this->response($response, 'Deleted successfully', 204);
        }
    }

    public function update($request, $response, $args)
    {
        $document = Document::where('document_id', '=', $args['document_id'])
            ->where('user_id', '=', $this->getCurrentUserId($request)->id);

        $document->save();

        if ($document->id) {
            $payload[] = $document->output();
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 204,
                'documents' => $payload
            ]);
        } else {
            return $response->withStatus(400);
        }
    }

    public function validate($request, $response, $args)
    {
        $_isvalidated = $request->getParsedBodyParam('is_validated');

        $document = Document::where('document_id', '=', $args['document_id'])
            ->where('user_id', '=', $this->getCurrentUserId($request)->id);

        $document->save();

        if ($document->id) {
            $payload[] = $document->output();
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 204,
                'documents' => $payload
            ]);
        } else {
            return $response->withStatus(400);
        }
    }

    public function deleteAll($request, $response, $args)
    {
        $documents = Document::where('user_id', '=', $this->currentUser($request)->id);

        foreach ($documents as $_document) {
            $_document->delete();
        }

        $documents = Document::where('user_id', '=', $this->currentUser($request)->id);

        if ($documents->isEmpty()) {
            return $this->response($response, 'All documents have been deleted successfully', 400);
        } else {
            return $this->response($response, 'Unable to complete request', 400);
        }
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

}