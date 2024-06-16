<?php 
    session_start();
    $host = "localhost";
    $user = "root";
    $password = "123456789";
    $con = mysqli_connect($host, $user, $password);

    mysqli_select_db($con, "login"); 
?>