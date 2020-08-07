<?php
declare(strict_types=1);

namespace App\Action\Auth;

use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;
use App\Action;
use App\Repository;

final class LoginAction extends BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        $input = (array) $request->getParsedBody();
        var_dump($input);
        print("asdasdasd");
        $jwt = $this->getUserService()->login($input);
        $message = [
            'Authorization' => 'Bearer ' . $jwt;
        ];
        return $this->jsonResponse($response, 'success', $message, 200);
    }
}

?>
