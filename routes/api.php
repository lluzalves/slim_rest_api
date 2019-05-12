<?php

use App\Controllers\DocumentController;
use App\Controllers\EdictController;
use App\Controllers\NotificationController;
use App\Middleware\FileFilter as Filter;
use App\Controllers\UserController;
use App\Middleware\BasicAuth;


$app->group('/user', function () {
    $this->get('/all', UserController::class . ':requestUsers');
    $this->get('/all/edict/{edict_id}', UserController::class . ':requestUsersByEdict');
    $this->get('/{email}', UserController::class . ':retrieveUserByEmail');
    $this->get('/filter/{prontuario}', UserController::class . ':retrieveUserByProntuario');
    $this->delete('/{id}', UserController::class . ':delete');
    $this->put('/{email}', UserController::class . ':updateInfo');
});

$app->group("/notifications", function (){
   $this->get('',NotificationController::class . ':allUserNotifications');
    $this->post('/{user_id}', NotificationController::class . ':createNotification');
    $this->delete('/{notification_id}', NotificationController::class . ':deleteNotification');
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

$app->group('/edict', function () {
    $this->get('/all',EdictController::class . ':allEdicts');
    $this->get('/user/all', EdictController::class . ':allEdictsType');
    $this->get('/details/{edict_id}',EdictController::class . ':edictDetails');
    $this->post('/add', EdictController::class . ':create');
});

$app->group('/documents', function () {
    $this->get('', DocumentController::class . ':userDocuments');
    $this->get('/all', DocumentController::class . ':allDocuments');
    $this->get('/all/{edict_id}', DocumentController::class . ':allDocumentsForEdict');
    $this->get('/{document_id}', DocumentController::class . ':getDocument');
    $this->get('/{document_id}/attachment', DocumentController::class . ':getDocumentAttachment');
    $this->post('/validate/{document_id}', DocumentController::class . ':validate');
    $this->post('', DocumentController::class . ':userUpsert')->add(new Filter());
    $this->post('/upsert', DocumentController::class . ':adminUpsert')->add(new Filter());
    $this->delete('/{document_id}', DocumentController::class . ':delete');
    $this->put('/{document_id}', DocumentController::class . ':update');
});