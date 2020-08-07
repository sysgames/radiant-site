<?php
/*
    * [Middleware Handler]
    * Middleware allows you to run code before and after your Slim application
    * to manipulate Request and Response objects as you see fit.
    * Ex: authenticating requests before the app runs
*/

use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function(App $app){
    //Parse json, form data, and xml
    $app->addBodyParsingMiddleware();
    //Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();
    //$app->add(BasePathMiddleware::class);
    //Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
}
?>
