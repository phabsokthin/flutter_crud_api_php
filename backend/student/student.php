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
    $student = getData("tblstudent");
    echo $student;
} elseif ($requestMethod === 'POST') {
    if (isset($_FILES['image']) && (is_array($_FILES['image']['error']) || $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE)) {

        $sname = $_POST['sname'];

        $uploadedFiles = [];

        $error = $_FILES['image']['error'];
        if ($error === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $fileName = $file['name'];

            $uploadResult = uploadFileData($file, $fileName);

            if ($uploadResult['status'] === 200) {
                $uploadedFiles[] = $uploadResult['file_url'];
            } else {
                echo json_encode($uploadResult);
                exit;
            }
        }

        $tablename = "tblstudent";
        $fields = ["sname", "image"];
        $values = [
            $sname,
           
            json_encode($uploadedFiles)
        ];

        if (createData($tablename, $fields, $values)) {
            $response = [
                'status' => 201,
                'message' => 'student created successfully',
                'imageUrls' => $uploadedFiles
            ];
            header("HTTP/1.1 201 Created");
        } else {
            $response = ['status' => 500, 'message' => 'Error creating product'];
        }
    } else {
        $response = ['status' => 400, 'message' => 'image is required and must be valid files'];
    }
    echo json_encode($response);
}
