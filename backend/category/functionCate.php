<?php

require_once('../inc/dbcon.php');

function insert_data($tablename, $fields, $values)
{
    global $conn;

    if (count($fields) !== count($values)) {
        throw new Exception("Fields and values count do not match.");
    }

    $placeholders = implode(", ", array_fill(0, count($fields), "?"));
    $columns = implode(", ", $fields);
    $sql = "INSERT INTO $tablename ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    $types = str_repeat("s", count($values));
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}



function getData($tableName)
{
    global $conn;
    $query = "SELECT * FROM $tableName";
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


function getDataById($tableName, $columnsId, $id)
{
    global $conn;
    $id = mysqli_real_escape_string($conn, $id); // Secure the id
    $query = "SELECT * FROM $tableName WHERE $columnsId = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $id);     
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            return json_encode($data);
        } else {
            return json_encode([
                'status' => 404,
                'message' => 'Data not found'
            ]);
        }
    } else {
       
        return json_encode([
            'status' => 500,
            'message' => 'Internal server error'
        ]);
    }
}


function updateData($tableName, $fields, $whereColumn, $id)
{
    global $conn;

    $setClause = implode(", ", array_map(function ($key) {
        return "$key = ?";
    }, array_keys($fields)));


    $query = "UPDATE $tableName SET $setClause WHERE $whereColumn = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        return json_encode([
            'status' => 500,
            'message' => 'Failed to prepare statement: ' . $conn->error
        ]);
    }

    $types = str_repeat("s", count($fields)) . "i";
    $values = array_values($fields);
    $values[] = $id; 
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            return json_encode([
                'status' => 200,
                'message' => 'Data updated successfully'
            ]);
        } else {
            return json_encode([
                'status' => 404,
                'message' => 'Data not found or no changes made'
            ]);
        }
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Failed to execute query: ' . $stmt->error
        ]);
    }
}



function deleteData($tableName, $idColumn, $id)
{
    global $conn;
    $query = "DELETE FROM $tableName WHERE $idColumn = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        return json_encode(['status' => 500, 'message' => 'Failed to prepare statement: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);

    if (mysqli_stmt_execute($stmt)) {
        return json_encode(['status' => 200, 'message' => 'Data deleted successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Failed to delete data: ' . mysqli_error($conn)]);
    }
}
