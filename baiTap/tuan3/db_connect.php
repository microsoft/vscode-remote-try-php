<?php
// Kết nối với CSDL
    $db = new mysqli('localhost', 'username', 'password', 'users');

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
?>