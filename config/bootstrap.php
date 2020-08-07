<?php

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';
session_start();

//Build PHP-DI Container instance
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();

//Create App instance
$app = $container->get(App::class);
$app->setBasePath('/radiant-site/public');
(require __DIR__ . '/settings.php')($container); //Register settings
(require __DIR__ . '/repositories.php')($container); //Register repositories
(require __DIR__ . '/routes.php')($app); //Register routes
(require __DIR__ . '/middleware.php')($app); //Register middleware

return $app;
?>
