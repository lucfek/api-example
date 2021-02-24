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

$jwt=isset($data->jwt) ? $data->jwt : "";

$user->firstname = isset($data->firstname) ? $data->firstname : "";
$user->lastname = isset($data->lastname) ? $data->lastname : "";
$user->phone = isset($data->phone) ? $data->phone : "";
$user->password = isset($data->password) ? $data->password : "";
$user->uuid = isset($data->uuid) ? $data->uuid : "";

if($jwt){
    try {
        $decoded = JWT::decode($jwt, JWT_KEY, array('HS256'));
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
        return;
    }
    if(!$user->validate()) {
        http_response_code(403);
        echo json_encode(
            array("message" => "Validation errors.",
                "validation_errors" => $user->validator_errs)
        );
        return;
    }
    try {
        $user->update();
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Internal server error.",
            "error" => $e->getMessage()
        ));
        return;
    }

    $token = array(
        "iat" => time(),
        "exp" => time() + JWT_EXP_TIME,
        "data" => array(
            "id" => $user->uuid,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email,
            "phone" => $user->phone,
        )
    );

    http_response_code(200);

    $jwt = JWT::encode($token,JWT_KEY);
    echo json_encode(
        array(
            "message" => "User was updated",
            "jwt" => $jwt,
            "user" => $user
        )
    );
}else{
    http_response_code(401);
    echo json_encode(array("message" => "Access denied"));
}
