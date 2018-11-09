<?php

include 'bootstrap.php';

require __DIR__ . '/../vendor/autoload.php';

use App\Middleware\Logger as Logger;
use App\Middleware\Authentication as Auth;

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ]
]);

$app->add(function ($request, $response, $next) {
    $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    $response = $next ($request, $response);
    return $response;
});

$app->add(new Auth());
$app->add(new Logger());

$container = $app->getContainer();
$container ['view'] = function ($container) {

    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

require __DIR__ . '/../routes/api.php';