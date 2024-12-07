<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers to handle CORS and HTTP methods
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("./functionCate.php"); // Assuming this file contains the reusable `insert_data` function

$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($requestMethod === "GET") {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $response = getDataById("category", "id", $id);
        echo $response;
    } else {
        $customerList = getData(tableName: 'category');
        echo $customerList;
    }
}


if ($requestMethod === "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->cname) && !empty($data->cname) && isset($data->detail) && !empty($data->detail)) {
        try {
            $tablename = "category";
            $fields = ["cname", "detail"];
            $values = [$data->cname, $data->detail];

            if (insert_data($tablename, $fields, $values)) {
                $response = [
                    'status' => 201,
                    'message' => 'Category created successfully.'
                ];
                header("HTTP/1.1 201 Created");
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Failed to create category.'
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
            'message' => 'Both cname and detail are required.'
        ];
        header("HTTP/1.1 400 Bad Request");
    }

    echo json_encode($response);
}

if ($requestMethod === "PUT") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id) && isset($data->cname) && isset($data->detail)) {
        $updateFields = [
            'cname' => $data->cname,
            'detail' => $data->detail
        ];

        $response = updateData("category", $updateFields, 'id', $data->id);
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'ID, cname, and detail are required.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }
}


if ($requestMethod === "DELETE") {
    parse_str(file_get_contents(filename: "php://input"), $data);
    if (isset($data['id']) && is_numeric($data['id'])) {
        $response = deleteData('category', 'id', intval($data['id']));
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'A valid ID is required.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }
}
