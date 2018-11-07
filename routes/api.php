<?php

use App\Controllers\DocumentController;

$app->group('/documents', function(){
    $this->get('', DocumentController::class.':all');
    $this->post('',DocumentController::class.':add');
    $this->delete('',DocumentController::class.':deleteAll');
    $this->delete('/{document_id}',DocumentController::class.':delete');
});