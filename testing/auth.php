<?php
require("vendor/autoload.php");
use \Firebase\JWT\JWT;

/*
    After checking user credentials (Authenticating)
*/

//Get user data
$player_id = 0;
$email = "";
$nickname = "";
$level = "";
$scope = "";

//Generate token
$secret_key = "static_secret_key_no_one_will_ever_guess";
$issuer = "panjaco.com";
$iat = time();
$nbf = $iat + 10;
$exp = $iat + 60;

$token = array(
    "iss" => $issuer,
    "iat" => $iat,
    "nbf" => $nbf,
    "exp" => $exp,
    "data" => array(
        "player_id" => $player_id,
        "email" => $email,
        "nickname" => $nickname,
        "level" =>  $level,
        "scope" => $scope
    )
);

http_response_code(200); //Just API stuff, 200=OK
$jwt = JWT::encode($token, $secret_key);
echo json_encode(
    array(
        "message" => "Successful login.",
        "jwt" => $jwt,
        "email" => $email,
        "expireAt" =>  $exp
    )
);

/*
    If user failed authentication
*/
http_response_code(401);
echo json_encode(
    array(
        "message" => "Login failed."
    )
);
?>
