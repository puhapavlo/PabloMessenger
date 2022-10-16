<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Firebase\JWT\JWT;
use PabloMessenger\api\authentication\User;
use PabloMessenger\api\config\Database;

include_once "../config/core.php";

header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$email_exists = $user->emailExists();

if ($email_exists && password_verify($data->password, $user->password)) {

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email
        )
    );

    http_response_code(200);

    $jwt = JWT::encode($token, $key, 'HS256');
    echo json_encode(
        [
            "message" => "Login successful.",
            "jwt" => $jwt
        ]
    );

}

else {

    http_response_code(401);

    echo json_encode(array("message" => "Login failed."));
}
