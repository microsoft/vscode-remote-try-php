<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm kiểm tra đăng nhập
    public function login($username, $password) {
        // Viết mã kiểm tra đăng nhập ở đây
    }
}
?>
