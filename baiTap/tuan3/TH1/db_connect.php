<?php
// Kết nối với CSDL
    $db = new mysqli('localhost', 'root', '', 'db_test');

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
?>