<?php
declare(strict_types=1);

namespace App\Action\Player;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\Container;
use App\Action;
use App\Repository;
use Slim\Routing\RouteContext;

final class GetOne extends \App\Action\BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        //var_dump($request);
        $id = (int) RouteContext::fromRequest($request)->getRoute()->getArguments()['id'];
        $player = $this->getUserRepository()->getUser($id);
        unset($player->password);
        if(!$player) return $this->jsonResponse($response, 'failure', null, 400);
        return $this->jsonResponse($response, 'success', $player, 200);
    }
}

?>
