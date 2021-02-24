<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once '../../config/Database.php';
include_once '../../models/User.php';
include_once '../../config/config.php';

use Firebase\JWT\JWT;

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate User
$user = new User($db);


// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

$user->email = isset($data->email) ? $data->email : "";
$password = isset($data->password) ? $data->password : "";

// Check login and email
if($user->read_single_by_email() && password_verify($password, $user->password)){
    $token = array(
        "iat" => time(),
        "exp" => time() + JWT_EXP_TIME,
        "data" => array(
            "uuid" => $user->uuid,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email,
            "phone" => $user->phone,
        )
    );

    // set response code
    http_response_code(200);

    // generate jwt
    $jwt = JWT::encode($token,JWT_KEY);
    echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt
        )
    );
    return;
}

http_response_code(401);
echo json_encode(
    array(
        "message" => "Login failed.",
    )
);