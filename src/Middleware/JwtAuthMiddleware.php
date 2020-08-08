<?php
namespace App\Middleware;
use \DI\Container;
use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class JwtAuthMiddleware implements MiddlewareInterface{
    /*
        * [JWT Auth Middleware]
        * Run this middleware when a valid JWT is required
    */
    private $container;

    public function __construct(Container $container){
        $this->container = $container;
    }

    private function getInvalidResponse(){
        $ir = $this->container->get('ResponseFactoryInterface')->createResponse()->withHeader('Content-Type', 'application/json')->withStatus(401, 'Unauthorized');
        $ir->getBody()->write(json_encode([
            'code' => 401,
            'status' => 'unauthorized',
            'message' => null,
        ]));
        return $ir;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $invalidResponse = $this->getInvalidResponse();
        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';
        if(empty($token)){
            //No token sent
            return $invalidResponse;
        }
        try{
            $decoded = (array) JWT::decode($token, $this->container->get('settings')['jwt']['secret'], array('HS256'));
            //If it did not throw an error, the token is valid
            return $handler->handle($request); //handle($request) fetches what would normally return from the other middleware and the actual route
        }catch(\Throwable $e){
            return $invalidResponse;
        }catch(\Exception $e){
            return $invalidResponse;
        }
    }
}

?>
