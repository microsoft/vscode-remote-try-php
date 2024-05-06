<?php
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "travelEasy";
$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idTour = $_POST['idTour'];
    $name = $_POST['name'];
    $startDay = $_POST['startDay'];
    $endDay = $_POST['endDay'];
    $cost = $_POST['cost'];

    // Chuẩn bị truy vấn UPDATE để cập nhật thông tin sinh viên
    $update_query = "UPDATE tblTour SET name = '$name', startDay = '$startDay', endDay = '$endDay', cost = '$cost' WHERE idTour = '$idTour'";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Tour đã được cập nhật thành công.'); window.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật thông tin Tour: " . mysqli_error($con) . "');</script>";
    }
}

mysqli_close($con);
?>
