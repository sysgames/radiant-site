<?php
require("vendor/autoload.php");
use \Firebase\JWT\JWT;

$key = "my_very_secret_key";
$payload = array(
    "account_id" => "5",    //
    "username" => "iPanja", //  User data
    "admin" => true,          //
    "iat" => time(), //Issued at (a time expressed in seconds)
    "nbf" => time()//Works after
    //exp => xxxxxxxxxx //Expires after
);
JWT::$leeway = 60; //60 seconds
$jwt = JWT::encode($payload, $key);
sleep(4);
$decoded = JWT::decode($jwt, $key, array('HS256'));
var_dump($decoded);
$decoded_array = (array) $decoded;
var_dump($decoded_array);


?>
