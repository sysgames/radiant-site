<?php
namespace App\Middleware;
use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;

class JwtAuthMiddleware implements MiddlewareInterface{
    /*
        * [JWT Auth Middleware]
        * Run this middleware when a valid JWT is required
    */
    private $container;
    private $settings;

    public function __construct(Container $container){
        $this->container = $container;
        $settings = $container['settings']['jwt'];
    }

    public function __invoke(Request $request, RequestHandler $handler): Response{
        $token = explode(' ', (string)$request->getHeaderLine('Authorization'))[1] ?? '';
        $conn = $container->get('connection');
        $player_id = $decoded['data']['player_id'];
        try{
            $decoded = (array) JWT::decode($token, $this->app->get);
            //Check JTI matches the one in the DB
        }catch(ExpiredException $e){
            //Token expired, check if the refresh token is still valid
            $stmt = $conn->prepare("SELECT * FROM api WHERE player_id=?");
            $stmt->bindParam(1, $player_id);
        }catch(Exception $e){
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401, 'Unauthorized');
        }
        $data = (array) $request->getParsedBody();
        $email = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');
    }
}

?>
