<?php

namespace App\Controllers;

use App\Models\Document;
use App\Models\User;
use Slim\Http\UploadedFile;

class DocumentController extends BaseController
{

    protected $filename;
    protected $payload = [];
    protected $currentUser;

    public function userDocuments($request, $response)
    {
        $currentUser = $this->currentUser($request);

        if ($currentUser[0]->role == "admin") {
            $documents = Document::where('user_id', '=', $request->getQueryParams('user_id', ''))->get();
        } else {
            $documents = Document::where('user_id', '=', $currentUser[0]->id)->get();
        }
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

    public function allDocuments($request, $response)
    {
        $currentUser = $this->currentUser($request);

        if ($currentUser[0]->role == "admin") {
            $documents = Document::all();

            if (count($documents) <= 0) {
                return $this->response($response, 'No documents having been uploaded yet', 204);
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
    }

    public function getDocument($request, $response, $args)
    {
        $currentUser = $this->currentUser($request);

        if ($currentUser->role = 'admin') {
            $document = Document::where('id', '=', $args['document_id'])->take(1)->get();
        } else {
            $document = Document::where('id', '=', $args['document_id'])
                ->where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();
        }

        if ($document[0]->exists) {
            $payload[] = $document[0]->output();
            return $response->withStatus(200)
                ->withJson([
                    'message' => 'Success',
                    'code' => 204,
                    'documents' => $payload
                ]);
        } else {
            return $response->withStatus(400);
        }
    }

    public function upsert($request, $response, $directory, $currentUser, $type)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $response->write('uploaded ' . $filename . '<br/>');
        }

        $id = $request->getParsedBodyParam('id', '');

        if (!empty($id)) {
            $documents = Document::where('id', '=', $id)
                ->where('user_id', '=', $currentUser->id)->take(1)->get();
            $document = $documents[0];
        } else {
            $document = new Document();
        }
        $document->description = $request->getParsedBodyParam('description', '');
        $document->user_id = $currentUser->id;
        $document->is_validated = false;
        $document->type = $type;
        $document->notification = 'Pendente';
        $document->file_url = '/opt/lampp/htdocs/slim_app/raw' . DIRECTORY_SEPARATOR . $currentUser->prontuario . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $filename;
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

    public function userUpsert($request, $response)
    {
        $currentUser = $this->currentUser($request);
        $type = $request->getParsedBodyParam('type', '');
        $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'raw' . DIRECTORY_SEPARATOR . $currentUser[0]->prontuario . DIRECTORY_SEPARATOR . $type;

        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }
        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }

        $this->upsert($request, $response, $directory, $currentUser[0], $type);
    }

    public function adminUpsert($request, $response)
    {
        $currentUser = $this->currentUser($request);
        if ($currentUser[0]->role = 'admin') {
            $targetUser = $this->targetUser($request->getParsedBodyParam('user_id'));
            $type = $request->getParsedBodyParam('type', '');
            $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'raw' . DIRECTORY_SEPARATOR . $targetUser[0]->prontuario . DIRECTORY_SEPARATOR . $type;

            if (!is_dir($directory)) {
                mkdir($directory, 0700, true);
            }
            $files = glob($directory . '/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
            $this->upsert($request, $response, $directory, $targetUser[0], $type);
        }
    }


    public function delete($request, $response, $args)
    {
        $currentUser = $this->currentUser($request);

        if ($currentUser->role = 'admin') {
            $document = Document::where('id', '=', $args['document_id'])->take(1)->get();
        } else {
            $document = Document::where('id', '=', $args['document_id'])
                ->where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();
        }

        if ($document[0]->exists) {
            if ($document[0]->is_validated == 1) {
                return $this->response($response, 'Unable to complete request, document is already validated', 401);
            } else {
                $document[0]->delete();
                unlink($document[0]->file_url);
                if (!$document[0]->exists) {
                    return $this->response($response, 'Deleted successfully', 204);
                }
            }
        } else {
            return $this->response($response, 'Unable to complete request', 401);
        }
    }


    public function validate($request, $response, $args)
    {
        $_isvalidated = $request->getQueryParams('is_validated');
        $document_id = $request->getQueryParams('document_id');

        var_dump($_isvalidated);
        var_dump($document_id);
        exit();

        $currentUser = $this->currentUser($request);

        if ($currentUser->role = 'admin') {
            $document = Document::where('id', '=', $args['document_id'])->take(1)->get();
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
    }

    public function deleteAll($request, $response, $args)
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

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }


    public function getDocumentAttachment($request, $response, $args)
    {
        $currentUser = $this->currentUser($request);

        if ($currentUser->role = 'admin') {
            $document = Document::where('id', '=', $args['document_id'])->take(1)->get();
        } else {
            $document = Document::where('id', '=', $args['document_id'])->
            where('user_id', '=', $this->currentUser($request)[0]->id)->take(1)->get();
        }
        if ($document[0]->exists) {
            $payload[] = $document[0]->fileOutput();
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
                ->withHeader('Content-Length', filesize($file))
                ->withHeader('Cache-Control', 'must-revalidate')
                ->withHeader('Pragma', 'public')
                ->withBody($stream);

        } else {
            return $response->withStatus(400);
        }
    }

}