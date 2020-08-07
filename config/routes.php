<?php
/*
    * [Route Handler]
    * Register all routes along with what middleware they use
*/
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function(App $app){
    $app->get('/', \App\Action\HomeAction::class);
    //Authentication and Authorization
    $app->post('/tokens', \App\Action\Auth\TokenCreateAction::class);
    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', \App\Action\Auth\LoginAction::class); //User login wtih email, password
        $group->post('/refresh', \App\Action\Auth\RefreshToken::class); //Silently refresh (make a new JWT and refresh token)
    });
    $app->group('/test', function(RouteCollectorProxy $group){
        $group->get('/user/{id}', \App\Action\Users::class); //Get all users
    });
};

?>
