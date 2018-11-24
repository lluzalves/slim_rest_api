<?php

namespace App\Middleware;


class FileFilter
{


    protected $allowedFiles = ["image/jpeg", "image/jpg", "image/png", "application/pdf"];

    public function __invoke($request, $response, $next)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $newFile = $uploadedFiles['file'];
        $newFileType = $newFile->getClientMediaType();
        if (!in_array($newFileType, $this->allowedFiles)) {
            return $response->withStatus(415)->withJson([
                'message' => 'Failed, unsupported file',
                'code' => 415,
                'file' => $request
            ]);
        }

        $response = $next($request, $response);

        return $response;
    }

}