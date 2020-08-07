<?php
/*
    * [Container Handler]
    * Dependency injection (container)
*/

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Selective\BasePath\BasePathMiddleware;
use \DI\Container;

return [
    'settings' => function(){
        return require __DIR__ . '/settings.php';
    },
    App::class => function(ContainerInterface $container){
        AppFactory::setContainer($container);
        return AppFactory::create();
    },
    ErrorMiddleware::class => function (ContainerInterface $container) {
        $app = $container->get(App::class);
        $settings = $container->get('settings')['error'];

        return new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details']
        );
    },
    PDO::class => function(ContainerInterface $container){
        $settings = $container->get('settings')['connection'];
        $host = $settings['host'];
        $dbname = $settings['dbname'];
        $dbuser = $settings['dbuser'];
        $dbpass = $settings['dbpass'];

        try{
            $connection = new PDO("mysql:host={$host};dbname={$dbname}", $dbuser, $dbpass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
        }

        return $connection;
    }
    /*
    BasePathMiddleware::class => function(ContainerInterface $container){
        return new BasePathMiddleware($container->get(App::class));
    },*/
];

?>
