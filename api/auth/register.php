<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../models/OTP.php';
include_once '../../config/Database.php';
include_once '../../models/User.php';


// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate User
$user = new User($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

$user->email = isset($data->email) ? $data->email : '';
$user->firstname = isset($data->firstname) ? $data->firstname : "";
$user->lastname = isset($data->lastname) ? $data->lastname : "";
$user->phone = isset($data->phone) ? $data->phone : '';
$user->password = isset($data->password) ? $data->password : '';


$otp = new OTP($db, $data->email);

try {
    if(!$user->validate()) {
        http_response_code(403);
        echo json_encode(
            array("message" => "Validation errors.",
                "validation_errors" => $user->validator_errs)
        );
        return;
    }
    if($user->read_single_by_email()) {
        http_response_code(409);
        echo json_encode(
            array("message" => "User already exist.")
        );
        return;
    }
    if(!$otp->challenge(isset($data->otp) ? $data->otp : '')) {
        http_response_code(409);
        echo json_encode(
            array("message" => $otp->error)
        );
        return;
    }
    $user->create();
    http_response_code(200);
    echo json_encode(
        array("message" => "User registered.")
    );
}catch (Exception $e) {
    http_response_code(500);
    echo json_encode(
        array("message" => "Internal Server Error.",
        "error" => $e->getMessage())
    );
}
