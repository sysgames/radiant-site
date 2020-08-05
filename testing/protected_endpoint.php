<?php
require("vendor/autoload.php");
use \Firebase\JWT\JWT;
//require('connect.php')

$secret_key = "static_secret_key_no_one_will_ever_guess"; //SAME KEY USED EVERYWHERE
$jwt = null;
if($jwt){
    try{
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        //Access granted (access denied will throw an error and go to the catch)
        //Do whatever this endpoint is supposed to do
        echo json_encode(array(
            "message" => "Access granted."
        ));
    }catch(Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}

?>
