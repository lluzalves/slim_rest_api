<?php

use App\Controllers\DocumentController;
use App\Middleware\FileFilter as Filter;

$app->group('/public', function () {
    $this->get('', HomeController::class . ':login');
});

$app->group('/documents', function () {
    $this->get('', DocumentController::class . ':all');
    $this->post('', DocumentController::class . ':add')->add(new Filter());
    $this->delete('', DocumentController::class . ':deleteAll');
    $this->delete('/{document_id}', DocumentController::class . ':delete');
    $this->put('/{document_id}', DocumentController::class . ':update');
});