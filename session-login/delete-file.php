<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "web_pka";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    $sql = "SELECT * FROM files WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileName = $row["name"];

        $filePath = "uploads/" . $fileName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $sql = "DELETE FROM files WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "Tệp $fileName đã được xóa thành công.";
        } else {
            echo "Lỗi khi xóa tệp: " . $conn->error;
        }
    } else {
        echo "Không tìm thấy tệp cần xóa.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}

$conn->close();
?>
