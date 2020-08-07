<?php
declare(strict_types=1);
namespace App\Repository;
use \PDO;
use \Firebase\JWT\JWT;

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
    public function createRefreshToken(): string{ //Create a Refresh token (string)
        $settings = $this->container->get('settings')['jwt'];
        $payload = [
            'iss' => $settings['issuer'],
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + settings['r_lifetime'],
        ];
        return JWT::encode($payload, $settings['secret']);
    }
    public function refreshToken(string $refresh_token){ //Returns object($jwt, $refresh_token) if refresh_token is valid, otherwise false
        $entry = $this->getRefreshTokenEntry($refresh_token);
        //If we can decode the refresh_token, then it is still valid so we should generate them a new JWT and Refresh token
        $settings = $this->container->get('settings')['jwt'];
        try{
            $decoded = JWT::decode($refresh_token, $settings['secret']);
            //Successfuly decoded, the refresh token is valid. Make a new JWT/Refresh token with the account ID found from $entry
            $account = $this->getUser($entry->player_id);
            if(!$account) return false; //This should never happen unless the player was deleted from the DB for some reason
            $jwt = $this->createToken($account);
            $refresh = $this->createRefreshToken();
            return (object) ['jwt' => $jwt, 'refresh' => $refresh];
        }catch(Exception $e){
            return false;
        }
    }
    /*
        [LOGIN]
    */
    public function login($email, $password){
        $account = $this->verify($email, $password);
        if(!$account) return false; //If account doesn't exist
        //Generate JWT token
        return $this->createToken($account);
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
