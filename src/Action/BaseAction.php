<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\UserRepository;
use DI\Container;

abstract class BaseAction{
    protected $container;

    public function __construct(Container $container){
        $this->container = $container;
    }
    protected function getUserRepository(): UserRepository{
        return $this->container->get('user_repository');
    }
    protected function jsonResponse(Response $response, string $status, $message, int $code): Response {
        $result = [
            'code' => $code,
            'status' => $status,
            'message' => $message,
        ];
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
        //return $response->withJson($result, $code, JSON_PRETTY_PRINT);
    }
}
