<?php

use App\Controllers\DocumentController;
use App\Middleware\FileFilter as Filter;
use App\Controllers\UserController;


$app->group('/user', function () {
    $this->get('/{name}', UserController::class . ':retrieve');
    $this->delete('/{id}', UserController::class . ':delete');
    $this->put('/{name}', UserController::class . ':updateInfo');
});

$app->group('/register', function () {
    $this->post('', UserController::class . ':create');
});

$app->group('/documents', function () {
    $this->get('', DocumentController::class . ':all');
    $this->get('/{document_id}', DocumentController::class . ':getDocument');
    $this->post('', DocumentController::class . ':upsert')->add(new Filter());
    $this->delete('/{document_id}', DocumentController::class . ':delete');
    $this->put('/{document_id}', DocumentController::class . ':update');
});