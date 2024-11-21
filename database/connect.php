<?php
$server = 'localhost:3306';
$user = 'root';
$pass = '';
$database = 'web_pka';

$connect = new mysqli(
    $server,
    $user,
    $pass,
    $database
);

if ($connect) {
    mysqli_query($connect, "SET NAMES 'utf8' ");
    echo "success";
} else {
    echo "error";
}

?>