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



if ($requestMethod === "GET") {

    if (isset($_GET["id"])) {
        $id = intval($_GET["id"]);
        $res = getDatasById('product', 'productId', $id);
        echo $res;
    } else {
        $customerList = getData('product');
        echo $customerList;
    }
} elseif ($requestMethod === 'POST') {
    if (isset($_FILES['ProductImage']) && (is_array($_FILES['ProductImage']['error']) || $_FILES['ProductImage']['error'] !== UPLOAD_ERR_NO_FILE)) {

        $productName = $_POST['productName'];
        $description = $_POST['description'];
        $categoryId = $_POST['categoryId'];
        $barcode = $_POST['barcode'];
        $expiredate = $_POST['expiredate'];
        $qty = $_POST['qty'];
        $unitPriceIn = $_POST['unitPriceIn'];
        $unitPriceOut = $_POST['unitPriceOut'];

        $uploadedFiles = [];

        $error = $_FILES['ProductImage']['error'];
        if ($error === UPLOAD_ERR_OK) {
            $file = $_FILES['ProductImage'];
            $fileName = $file['name'];

            $uploadResult = uploadFileData($file, $fileName);

            if ($uploadResult['status'] === 200) {
                $uploadedFiles[] = $uploadResult['file_url'];  
            } else {
                echo json_encode($uploadResult);
                exit;
            }
        }

        $tablename = "product";
        $fields = ["productName", "description", "categoryId", "barcode", "expiredate", "qty", "unitPriceIn", "unitPriceOut", "ProductImage"];
        $values = [
            $productName,
            $description,
            $categoryId,
            $barcode,
            $expiredate,
            $qty,
            $unitPriceIn,
            $unitPriceOut,
            json_encode($uploadedFiles) 
        ];

        if (createData($tablename, $fields, $values)) {
            $response = [
                'status' => 201,
                'message' => 'Product created successfully',
                'imageUrls' => $uploadedFiles 
            ];
            header("HTTP/1.1 201 Created");
        } else {
            $response = ['status' => 500, 'message' => 'Error creating product'];
        }
    } else {
        $response = ['status' => 400, 'message' => 'ProductImage is required and must be valid files'];
    }
    echo json_encode($response);



} elseif ($requestMethod === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->productId) && isset($data->productName) && isset($data->description) && isset($data->categoryId) && isset($data->barcode) && isset($data->expiredate) && isset($data->qty) && isset($data->unitPriceIn) && isset($data->unitPriceOut) && isset($data->ProductImage)) {

        $updateFields = [
            "productName" => $data->productName,
            "description" => $data->description,
            "categoryId" => $data->categoryId,
            "barcode" => $data->barcode,
            "expiredate" => $data->expiredate,
            "qty" => $data->qty,
            "unitPriceIn" => $data->unitPriceIn,
            "unitPriceOut" => $data->unitPriceOut,
            "ProductImage" => $data->ProductImage,
        ];

        $response = updateDatas('product', $updateFields, 'productId', $data->productId);
        echo $response;
    } else {
        $errorData = [
            'status' => 400,
            'message' => 'All required fields are needed.'
        ];
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($errorData);
    }
} elseif ($requestMethod === "DELETE") {
    parse_str(file_get_contents(filename: "php://input"), $data);
    if (isset($data['id']) && is_numeric($data['id'])) {
        $response = deleteDatas('product', 'productId', intval($data['id']));
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
