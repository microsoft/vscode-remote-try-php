<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm kiểm tra đăng nhập
    public function login($username, $password) {
        $sql = "SELECT * FROM User WHERE TenUser='$username' AND MatKhau='$password'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>
