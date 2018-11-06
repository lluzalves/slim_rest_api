<?php

$app-> get('/home', function($request, $response){
    $this->view->render($response, 'home.twig');
});