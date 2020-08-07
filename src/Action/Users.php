<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\Container;
use App\Action;
use App\Repository;
use Slim\Routing\RouteContext;

final class Users extends BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        //var_dump($request);
        $id = (int) RouteContext::fromRequest($request)->getRoute()->getArguments()['id'];
        $user = $this->getUserRepository()->getUser($id);
        if(!$user) return $this->jsonResponse($response, 'failure', null, 401)->withStatus(401);
        return $this->jsonResponse($response, 'success', $user, 200)->withStatus(200);
    }
}

?>
