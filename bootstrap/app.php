<?php

include 'bootstrap.php';

require __DIR__ . '/../vendor/autoload.php';

use App\Middleware\Logging as AppLogging;
use App\Middleware\Authentication as Auth;

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails'  => true,
    ]
]);

// $app->add(new Auth());
$app->add(new AppLogging());


//retrive container
$container = $app->getContainer();

//register component on container
$container ['view'] = function ($container){
    
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views',[
        'cache' => false,
    ]);

    //instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension( new \Slim\Views\TwigExtension($router,$uri));

    return $view;
};

require __DIR__.'/../routes/web.php';
require __DIR__.'/../routes/api.php';