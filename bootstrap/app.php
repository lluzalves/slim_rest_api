<?php

include 'bootstrap.php';

require __DIR__ . '/../vendor/autoload.php';

use App\Middleware\Logger as Logger;
use App\Middleware\TokenAuth as TokenAuth;
use App\Middleware\BasicAuth as BasicAuth;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

$app = new App([
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

$request = $app->getContainer()->get('request');
$requestedUri = $request->getServerParams()['REQUEST_URI'];
if (strpos($requestedUri, '/slim_app/public/documents') !== false ||
    strpos($requestedUri, '/slim_app/public/user') !== false  ||
    strpos($requestedUri, '/slim_app/public/edict') !== false  ||
strpos($requestedUri, '/slim_app/public/notifications') !== false) {
    $app->add(new TokenAuth());
} else if (strpos($requestedUri, '/slim_app/public/register') !== false || strpos($requestedUri, '/slim_app/public/recover') !== false) {

} else {
    $app->add(new BasicAuth());
}


$app->add(new Logger());

$container = $app->getContainer();
$container ['view'] = function ($container) {

    $view = new Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);
    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    return $view;
};

require __DIR__ . '/../routes/api.php';

