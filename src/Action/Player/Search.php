<?php
declare(strict_types=1);

namespace App\Action\Player;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\Container;
use App\Action;
use App\Repository;
use Slim\Routing\RouteContext;

final class Search extends \App\Action\BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        //var_dump($request);
        $query = (string) RouteContext::fromRequest($request)->getRoute()->getArguments()['query']; //Nickname or email
        $players = $this->getUserRepository()->search($query);

        if(!$players) return $this->jsonResponse($response, 'failure', null, 400);
        return $this->jsonResponse($response, 'success', $players, 200);
    }
}

?>
