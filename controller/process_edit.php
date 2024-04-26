<?php
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "travel";
$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idEmployee = $_POST['idEmployee'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Chuẩn bị truy vấn UPDATE để cập nhật thông tin sinh viên
    $update_query = "UPDATE tblEmployee SET name = '$name', phone = '$phone', email = '$email', address = '$address' WHERE idEmployee = '$idEmployee'";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Thông tin sinh viên đã được cập nhật thành công.'); window.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật thông tin sinh viên: " . mysqli_error($con) . "');</script>";
    }
}

mysqli_close($con);
?>
