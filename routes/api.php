<?php

use App\Controllers\DocumentController;
use App\Middleware\BaseAuth;
use App\Middleware\FileFilter as Filter;
use App\Controllers\UserController;
use App\Middleware\BasicAuth;


$app->group('/user', function () {
    $this->get('/{email}', UserController::class . ':retrieve');
    $this->delete('/{id}', UserController::class . ':delete');
    $this->put('/{email}', UserController::class . ':updateInfo');
});

$app->group('/register', function () {
    $this->post('', UserController::class . ':create');
});

$app->group('/login', function () {
    $this->post('', BasicAuth::class . ':create');
});

$app->group('/recover', function () {
    $this->post('', UserController::class . ':recoverCredentials');
});

$app->group('/documents', function () {
    $this->get('', DocumentController::class . ':all');
    $this->get('/{document_id}', DocumentController::class . ':getDocument');
    $this->get('/{document_id}/attachment', DocumentController::class . ':getDocumentAttachment');
    $this->post('', DocumentController::class . ':upsert')->add(new Filter());
    $this->delete('/{document_id}', DocumentController::class . ':delete');
    $this->put('/{document_id}', DocumentController::class . ':update');
});