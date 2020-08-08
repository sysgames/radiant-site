<?php
declare(strict_types=1);

namespace App\Action\Auth;

use Slim\App;
use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use DI\Container;
use App\Action;
use App\Repository;
use Slim\Routing\RouteContext;
use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;

final class LoginAction extends \App\Action\BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        $input = (array) $request->getParsedBody();
        //Check if necessary parameters are given
        $required = ['email', 'password'];
        foreach($required as $r){
            if(!isset($input[$r]) || empty($input[$r])){
                return $this->jsonResponse($response, 'failure', null, 400); //Necessary parameters not given
            }
        }
        $result = $this->getUserRepository()->login($input['email'], $input['password']);
        if(!$result){
            return $this->jsonResponse($response, 'failure', null, 400);
        }
        $message = [
            'Authorization' => 'Bearer ' . $result->jwt,
        ];
        $response = $this->getUserRepository()->setSecuredCookie($response, $result->refresh); //Put the refresh token in an HttpOnly cookie
        return $this->jsonResponse($response, 'success', $message, 200);
    }
}

?>
