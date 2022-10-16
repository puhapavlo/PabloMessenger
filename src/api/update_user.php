<?php

require_once __DIR__ . '/../../vendor/autoload.php';
include_once "config/core.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PabloMessenger\api\authentication\User;
use PabloMessenger\api\config\Database;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, new Key($key, "HS256"));
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;


        if ($user->update()) {
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
            $jwt = JWT::encode($token, $key, "HS256");

            http_response_code(200);

            echo json_encode(
                array(
                    "message" => "User was update",
                    "jwt" => $jwt
                )
            );
        } else {
            http_response_code(401);

            echo json_encode(array("message" => "Unable update user."));
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access Denied.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(401);

    echo json_encode(array("message" => "Access Denied."));
}
