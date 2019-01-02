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

    public function getDocument($request, $response, $args)
    {
        $document = Document::where('id', '=', $args['document_id'])
            ->where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();
        if ($document[0]->exists) {
            $payload[] = $document[0]->output();
            $file = $document[0]->file_url;
            $openFile = fopen($file, 'rb');
            $stream = new \Slim\Http\Stream($openFile);
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/force-download')
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Type', 'application/download')
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Disposition', 'attachment; filename="' . basename($file) . '"')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->withHeader('Pragma', 'public')
                ->withBody($stream)
                ->withJson([
                    'message' => 'Success',
                    'code' => 204,
                    'documents' => $payload
                ]);
        } else {
            return $response->withStatus(400);
        }
    }

    public function add($request, $response)
    {
        $currentUser = $this->currentUser($request);
        $type = $request->getParsedBodyParam('type', '');
        $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'raw' . DIRECTORY_SEPARATOR . $currentUser[0]->id . DIRECTORY_SEPARATOR . $type;
        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename . '<br/>');
        }

        $document = new Document();
        $document->description = $request->getParsedBodyParam('description', '');
        $document->user_id = $currentUser[0]->id;
        $document->is_validated = false;
        $document->type = $type;
        $document->file_url = 'C:\xampp\htdocs\slim_app\raw' . DIRECTORY_SEPARATOR . $currentUser[0]->id . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $filename;
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
        $document = Document::where('id', '=', $args['document_id'])
            ->where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();
        if ($document[0]->exists) {
            if ($document[0]->is_validated == 1) {
                return $this->response($response, 'Unable to complete request, document is already validated', 401);
            } else {
                $document[0]->delete();
                if (!$document[0]->exists) {
                    return $this->response($response, 'Deleted successfully', 204);
                }
            }
        } else {
            return $this->response($response, 'Unable to complete request', 401);
        }
    }


    public
    function update($request, $response, $args)
    {
        $document = Document::where('id', '=', $args['document_id'])
            ->where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();

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

    public
    function validate($request, $response, $args)
    {
        $_isvalidated = $request->getParsedBodyParam('is_validated');

        $document = Document::where('id', '=', $args['document_id'])
            ->where('user_id', '=', $this->currentUser($request)[0]->id);

        $document->isvalidated = $_isvalidated;

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

    public
    function deleteAll($request, $response, $args)
    {
        $documents = Document::where('user_id', '=', $this->currentUser($request)[0]->id);

        foreach ($documents as $_document) {
            $_document->delete();
        }

        $documents = Document::where('user_id', '=', $this->currentUser($request)[0]->id);

        if ($documents->isEmpty()) {
            return $this->response($response, 'All documents have been deleted successfully', 400);
        } else {
            return $this->response($response, 'Unable to complete request', 400);
        }
    }

    public
    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

}