<?php
/*
    Depreciated
*/
namespace App\Action\Auth;

use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;

final class TokenCreateAction{
    private $settings;

    public function __construct(Container $container){
        $this->settings = $container->get('settings')['jwt'];
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface{

        //Create a JWT token
        $jti = random_bytes(32);
        $rti = random_bytes(32);
        $time = time();
        $payload = array(
            "iss" => $this->settings['issuer'],
            "iat" => $time,
            "nbf" => $time + 10,
            "exp" => ($time + 10) + $this->settings['lifetime'],
            "jti" => $jti,
            "rti" => $rti,
            "data" => [
                'player_id' => 1,
            ],
        );
    }
}

?>
