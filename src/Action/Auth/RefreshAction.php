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
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\Cookie;

//Create a new JWT and Refresh token if the refresh token passed in the HttpOnly Cookie is valid
final class RefreshAction extends \App\Action\BaseAction{
    public function __invoke(Request $request, Response $response): Response{
        $refresh = FigRequestCookies::get($request, 'refresh')->getValue();
        $decoded = $this->getUserRepository()->isValidRefreshToken($refresh);
        if(!$decoded){ //The refresh token is not valid (Expired, fake, etc)
            return $this->jsonResponse($response, 'failure', null, 400);
        }
        $account = $this->getUserRepository()->getUser((int) $decoded->player_id);
        if(!$account){ //Check if for some reason the account no longer exists
            return $this->jsonResponse($response, 'failure', null, 400);
        }
        //Create new JWT and Refresh token
        $jwt = $this->getUserRepository()->createToken($account);
        $refresh = $this->getUserRepository()->createRefreshToken($account->player_id); //Creates refresh token and updates the entry stored in the `api` table
        $response = $this->getUserRepository()->setSecuredCookie($response, $refresh);
        $message = [
            'Authorization' => 'Bearer ' . $jwt
        ];

        return $this->jsonResponse($response, 'success', $message, 200);
    }
}

?>
