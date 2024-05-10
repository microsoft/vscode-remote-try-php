<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$database = "web_pka";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($_FILES["file"]["name"]);

    $fileName = $_FILES["file"]["name"];
    $fileType = $_FILES["file"]["type"];
    $fileSize = $_FILES["file"]["size"];
    $fileTmpName = $_FILES["file"]["tmp_name"];
    $fileUploadDate = date("Y-m-d H:i:s");

    $currentDateTime = date("YmdHis");
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = $currentDateTime . "." . $fileExtension;

    $sql = "INSERT INTO files (name, type, size, upload_date) VALUES ('$newFileName', '$fileType', '$fileSize', '$fileUploadDate')";
    if ($conn->query($sql) === FALSE) {
        echo "Lỗi khi lưu thông tin tệp vào cơ sở dữ liệu: " . $conn->error;
    }

    if (move_uploaded_file($fileTmpName, $targetFile)) {
        header("Location: upload.php");
    } else {
        echo "Có lỗi xảy ra khi tải lên tệp.";
    }
} else {
    echo "Vui lòng chọn một tệp để tải lên.";
}

$conn->close();
?>
