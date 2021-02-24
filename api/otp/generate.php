<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/OTP.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();



// Get raw posted data
$data = json_decode(file_get_contents("php://input"));


$otp = new OTP($db, $data->email);


try {
    if(!$otp->validate()) {
        http_response_code(403);
        echo json_encode(
            array("message" => "Validation errors.",
                "validation_errors" => $otp->validator_errs)
        );
        return;
    }
    if($otp->generate()){
        http_response_code(200);
        echo json_encode(
            array("message" => "OTP generated and sent.")
        );
    }else {
        http_response_code(409);
        echo json_encode(
            array("message" => $otp->error)
        );
    }
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        array("message" => "Internal Server Error.",
            "error" => $e->getMessage())
    );
}
