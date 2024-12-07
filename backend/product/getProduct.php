<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Headers to handle CORS and HTTP methods
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("../composable/getDatableDocument.php");
$requestMethod = $_SERVER["REQUEST_METHOD"];


if ($requestMethod === "GET") {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];        
        $tableName = "product";
        $joinTable = "category";
        $onCondition = "product.categoryId = category.id";
        $columnId = "product.productId";
        $response = getDataJoinByID($tableName, $joinTable, $onCondition, $columnId, $id);
        echo $response;
    } else {
        $tableName = "product";
        $joinTable = "category";
        $onCondition = "product.categoryId =category.id";
        $response = getDataJoin($tableName, $joinTable, $onCondition);
        echo $response;
    }
}
