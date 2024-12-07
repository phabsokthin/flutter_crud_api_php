<?php

    $host = 'localhost';
    $username = "root";
    $password = "";
    $dbname = "dbsokthin";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if(!$conn){
        die("Failed connect". mysqli_connect_error());
    }
?>