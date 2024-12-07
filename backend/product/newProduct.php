<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers to handle CORS and HTTP methods
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("../composable/useDatabase.php");
include("../composable/uploadFile.php");
$requestMethod = $_SERVER["REQUEST_METHOD"];



if ($requestMethod == "GET") {
    $newProduct = getData('tblproduct');
    echo $newProduct;
}
if ($requestMethod === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->productName) && !empty($data->productName) && isset($data->description) && !empty($data->description)) {
        try {
            $tablename = "tblproduct";
            $fields = ["productName", "description", 'barcode', 'qty', 'categoryId'];
            $values = [$data->productName, $data->description, $data->barcode, $data->qty, $data->categoryId];

            if (createData($tablename, $fields, $values)) {
                $response = [
                    'status' => 201,
                    'message' => 'product created successfully.'
                ];
                header("HTTP/1.1 201 Created");
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Failed to create product.'
                ];
                header("HTTP/1.1 500 Internal Server Error");
            }
        } catch (Exception $e) {
            $response = [
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
            header("HTTP/1.1 500 Internal Server Error");
        }
    } else {
        $response = [
            'status' => 400,
            'message' => 'Both productName and description are required.'
        ];
        header("HTTP/1.1 400 Bad Request");
    }

    echo json_encode($response);
}

?>