<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "web_pka";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPasswordFromDB = $row["password"];
            if (password_verify($password, $hashedPasswordFromDB)) {
                $_SESSION["username"] = $username;
                header("Location: index.php");
                exit;
            } else {
                echo "Tên đăng nhập hoặc mật khẩu không đúng.";
            }
        } else {
            echo "Tên đăng nhập hoặc mật khẩu không đúng.";
        }
    } else {
        echo "Vui lòng nhập tên đăng nhập và mật khẩu.";
    }
} else {
    header("Location: login.php");
    exit;
}

$conn->close();
?>
