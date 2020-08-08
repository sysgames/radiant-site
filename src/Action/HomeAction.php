<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomeAction extends \App\Action\BaseAction{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $about = $this->container->get('settings')['about'];
        $response->getBody()->write(json_encode([
            'success' => true,
            'api' => $about['name'],
            'version' => $about['version'],
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
