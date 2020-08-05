<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath("/radiant/index.php");
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Welcome to the Radiant API.");
    return $response;
});
$app->group('/player', function(){
    
});

$app->run();
?>
