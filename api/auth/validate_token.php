<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once '../../config/config.php';

use Firebase\JWT\JWT;


// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

$jwt=isset($data->jwt) ? $data->jwt : "";

if($jwt){
    try {
        $decoded = JWT::decode($jwt, JWT_KEY, array('HS256'));
        http_response_code(200);
        echo json_encode(
            array(
                "message" => "Access granted",
                "data" => $decoded->data
            )
        );
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied",
            "error" => $e->getMessage()
        ));
        return;
    }

    http_response_code(200);


}else{
    http_response_code(401);
    echo json_encode(array("message" => "Access denied"));
}