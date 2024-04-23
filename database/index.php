<?php
include "./connect.php";
$sql = "CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL,
    level INT(6) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);";

// Thực thi truy vấn
if ($connect->query($sql) === TRUE) {
    echo "done";
} else {
    echo "Error: " . $connect->error;
}
$connect->close();
?>