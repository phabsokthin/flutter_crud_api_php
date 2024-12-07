<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers to handle CORS and HTTP methods
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("function.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];
if ($requestMethod === "GET") {
    if (isset($_GET['id'])) {
        $customerId = $_GET['id'];
        $customerDetails = getCustomerById($customerId);
        echo $customerDetails;
    } else {
        $customerList = getCustomerList();
        echo $customerList;
    }

} elseif ($requestMethod === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->name) && isset($data->email)) {
        $response = createCustomer($data->name, $data->email);
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'Name and email are required.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }

} 

elseif ($requestMethod === "PUT") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id) && isset($data->name) && isset($data->email)) {
        $response = updateCustomer($data->id, $data->name, $data->email);
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'Customer ID, name, and email are required.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }

}


elseif ($requestMethod === "DELETE") {
    parse_str(file_get_contents("php://input"), $data);
    
    if (isset($data['id'])) {
        $response = deleteCustomer($data['id']);
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'Customer ID is required.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }
}





else {

    $data = [
        'status' => 405,
        'message' => "$requestMethod method not allowed",
    ];
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode($data);
}

?>
