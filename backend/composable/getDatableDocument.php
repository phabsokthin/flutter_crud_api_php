<?php
include("../inc/dbcon.php");


function getDataJoin($tableName, $joinTable, $onCondition)
{

    global $conn;
    $query = "SELECT * FROM $tableName 
              JOIN $joinTable 
              ON $onCondition";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            return json_encode($res);
        } else {
            return json_encode(['status' => 404, 'message' => 'No data found']);
        }
    } else {
        return json_encode(['status' => 500, 'message' => 'Internal server error']);
    }
}


function getDataJoinByID($tableName, $joinTable, $onCondition, $columnId, $id){
    global $conn;
    $query = "SELECT * FROM $tableName 
              JOIN $joinTable 
              ON $onCondition 
              WHERE $columnId = ?";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $id); 
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return json_encode($res);
        } else {
            return json_encode(['status' => 404, 'message' => 'No data found for the given ID']);
        }
    } else {
        return json_encode(['status' => 500, 'message' => 'Internal server error']);
    }
}


?>