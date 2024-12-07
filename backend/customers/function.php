<?php

require("../inc/dbcon.php");
function getCustomerList()
{
    global $conn;

    $query = "SELECT * FROM customers";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $res = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
            return json_encode($res);
        } else {
            return json_encode(['status' => 404, 'message' => 'No customers found']);
        }
    } else {
        return json_encode(['status' => 500, 'message' => 'Internal server error']);
    }
}

function getCustomerById($id)
{
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);

    $query = "SELECT * FROM customers WHERE id = '$id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $customer = mysqli_fetch_assoc($query_run);
            return json_encode([
                'status' => 200,
                'message' => 'Customer found',
                'data' => $customer
            ]);
        } else {
            return json_encode([
                'status' => 404,
                'message' => 'Customer not found'
            ]);
        }
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Internal server error'
        ]);
    }
}

function createCustomer($name, $email)
{
    global $conn;

    $query = "INSERT INTO customers (name, email) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $name, $email);

    if (mysqli_stmt_execute($stmt)) {
        return json_encode(['status' => 201, 'message' => 'Customer created successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Internal server error']);
    }
}

function updateCustomer($id, $name, $email) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);

    $query = "UPDATE customers SET name = '$name', email = '$email' WHERE id = '$id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        if (mysqli_affected_rows($conn) > 0) {
            return json_encode([
                'status' => 200,
                'message' => 'Customer updated successfully'
            ]);
        } else {
            return json_encode([
                'status' => 404,
                'message' => 'Customer not found'
            ]);
        }
    } else {
        return json_encode([
            'status' => 500,
            'message' => 'Internal server error'
        ]);
    }
}

function deleteCustomer($id)
{
    global $conn;
    $query = "DELETE FROM customers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        return json_encode(value: ['status' => 200, 'message' => 'Customer deleted successfully']);
    } else {
        return json_encode(['status' => 500, 'message' => 'Internal server error']);
    }
}
