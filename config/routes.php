<?php
/*
    * [Route Handler]
    * Register all routes along with what middleware they use
*/
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Middleware\JwtAuthMiddleware;

return function(App $app){
    $app->get('/', \App\Action\HomeAction::class);
    //Authentication and Authorization
    $app->post('/tokens', \App\Action\Auth\TokenCreateAction::class);
    $app->group('/auth', function (RouteCollectorProxy $group) {
        $group->post('/login', \App\Action\Auth\LoginAction::class); //User login wtih email, password
        $group->post('/refresh', \App\Action\Auth\RefreshAction::class); //Silently refresh (make a new JWT and refresh token)
    });
    $app->group('/player', function(RouteCollectorProxy $group){
        $group->get('/{id}', \App\Action\Player\GetOne::class); //Get user by {id}
        $group->get('/search/{query}', \App\Action\Player\Search::class)->add(JwtAuthMiddleware::class); //Get user by nickname matching {query}%
    });
};

?>
