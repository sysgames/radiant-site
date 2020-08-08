<?php
declare(strict_types=1);
namespace App\Repository;
use \PDO;
use \Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;

final class UserRepository extends BaseRepository{

    public function verify($email, $password){ //Returns either generic object holding the account or false
        $account = $this->getUserByEmail($email);
        return !$account ? false : (password_verify($password, $account->password) ? $account : false);
    }
    public function createToken(object $account): string{ //Create JWT token (string)
        $settings = $this->container->get('settings')['jwt'];
        $payload = [
            'iss' => $settings['issuer'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $settings['lifetime'],
            'player' => [
                'player_id' => $account->player_id,
                'level' => $account->level,
                'scope' => $account->scope,
            ],
        ];
        return JWT::encode($payload, $settings['secret']);
    }
    public function InsertOrUpdateRefreshToken($player_id, $refresh): void{
        $stmt = $this->getDb()->prepare("INSERT INTO api (player_id, rti) VALUES (?, ?) ON DUPLICATE KEY UPDATE rti=?");
        $stmt->bindParam(1, $player_id);
        $stmt->bindParam(2, $refresh);
        $stmt->bindParam(3, $refresh);
        $stmt->execute();
    }
    public function createRefreshToken($player_id): string{ //Create a Refresh token (string)
        $settings = $this->container->get('settings')['jwt'];
        $payload = [
            'iss' => $settings['issuer'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $settings['r_lifetime'],
            'player_id' => $player_id,
        ];
        $refresh = JWT::encode($payload, $settings['secret']);
        $this->InsertOrUpdateRefreshToken($player_id, $refresh);
        return $refresh;
    }
    protected function isTokenValid($token){ //Returns either decoded token or false
        $settings = $this->container->get('settings')['jwt'];
        try{
            $decoded = JWT::decode($token, $settings['secret'], array('HS256'));
            return $decoded;
        }catch(Exception $e){
            return false;
        }
    }
    public function refreshToken(string $refresh_token){ //Returns object($jwt, $refresh_token) if refresh_token is valid, otherwise false
        //If we can decode the refresh_token, then it is still valid so we should generate them a new JWT and Refresh token
        $settings = $this->container->get('settings')['jwt'];
        try{
            $decoded = JWT::decode($refresh_token, $settings['secret'], array('HS256'));
            //Successfuly decoded, the refresh token is valid. Make a new JWT/Refresh token with the account ID found from $entry
            $account = $this->getUser($entry->player_id);
            if(!$account) return false; //This should never happen unless the player was deleted from the DB for some reason
            $jwt = $this->createToken($account);
            $refresh = $this->createRefreshToken($account->player_id);
            return (object) ['jwt' => $jwt, 'refresh' => $refresh];
        }catch(Exception $e){
            return false;
        }
    }
    public function isValidRefreshToken($refresh) { //Returns decoded token or false
        $decoded = $this->isTokenValid($refresh);

        $stmt = $this->getDb()->prepare("SELECT 1 FROM api WHERE player_id=? AND rti=?");
        $stmt->bindParam(1, $decoded->player_id);
        $stmt->bindParam(2, $refresh);
        $stmt->execute();
        return $stmt->rowCount() == 1 ? $decoded : false;
    }
    /*
        [LOGIN]
    */
    public function login($email, $password){
        $account = $this->verify($email, $password);
        if(!$account) return false; //If account doesn't exist
        //Generate JWT token and Refresh token
        return (object) ['jwt' => $this->createToken($account), 'refresh' => $this->createRefreshToken($account->player_id)];
    }
    public function logout(Response $response){
        return $response->withHeader(
            'Set-Cookie',
            'Authentication=; HttpOnly; Secure; Path=/; Max-Age=0'
        );
    }
    public function getUser(int $player_id){ //Returns either generic object holding the account or false
        $stmt = $this->getDb()->prepare("SELECT * FROM players WHERE player_id=?");
        $stmt->bindParam(1, $player_id);
        $stmt->execute();
        $account = $stmt->fetchObject();
        return $account;
    }
    public function getUserByEmail(string $email){ //Returns either generic object holding the account or false
        $stmt = $this->getDb()->prepare("SELECT * FROM players WHERE email=?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $account = $stmt->fetchObject();
        return $account;
    }
    public function search(string $query){ //Returns generic object holding the player or false
        $stmt = $this->getDb()->prepare("SELECT * FROM players WHERE UPPER(nickname) LIKE UPPER(?)");
        $nickname = "$query%"; //$query msut be the exact beginning of usernames
        $stmt->bindParam(1, $nickname);
        $stmt->execute();
        $accounts = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($accounts as $account){
            unset($account->password);
        }
        return $accounts;
    }
    public function getRefreshTokenEntry(string $refresh_token){ //Returns either generic object holding an api table entry or false
        $stmt = $this->getDb()->prepare("SELECT * FROM api WHERE rti=?");
        $stmt->bindParam(1, $refresh_token);
        $stmt->execute();
        $entry = $stmt->getObject();
        return !$entry ? false : $entry;
    }
    public function revokeRefreshTokens(int $player_id){
        $stmt = $this->getDb()->prepare("DELETE FROM api WHERE player_id=?");
        $stmt->bindParam(1, $player_id);
        $stmt->execute();
    }
    public function setSecuredCookie(Response $response, $refresh){
        $response = FigResponseCookies::set($response, SetCookie::create('refresh')
            ->withValue($refresh)
            ->withDomain($this->container->get('settings')['domain'])
            ->withSecure(true)
            ->withHttpOnly(true)
        );
        return $response;
    }
    /*
    *   TODO [CRUD]
    */
    public function create(object $user): object{

    }
    public function update(object $user): object{

    }
    /*
    *   [Private methods]
    */
    private static function encrypt(string $password): string{
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
    }

}
