<?php
class LoginController {
    public function index() {
        // Hiển thị trang đăng nhập
        include '../views/login/index.php';
    }

    public function login($db) {
        require_once '../models/User.php';

        // Xử lý đăng nhập
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $userModel = new User($db);
            $result = $userModel->login($username, $password);

            if ($result) {
                session_start();
                $_SESSION['username'] = $username;
                header("location: ../controllers/SachController.php");
            } else {
                // Đăng nhập thất bại, chuyển hướng đến trang đăng nhập và hiển thị thông báo lỗi
                header("location: ../controllers/LoginController.php?error=1");
            }
        }
    }
}
?>
