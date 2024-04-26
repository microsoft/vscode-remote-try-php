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

// Lấy dữ liệu từ biểu mẫu 'add.php' sử dụng phương thức POST
$idEmployee = mysqli_real_escape_string($con, $_POST['idEmployee']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$phone = mysqli_real_escape_string($con, $_POST['phone']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$address = mysqli_real_escape_string($con, $_POST['address']);


$check_query = "SELECT * FROM tblEmployee WHERE idEmployee = '$idEmployee'";
$result = mysqli_query($con, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Nếu tồn tại bản ghi với mã sinh viên đã nhập
    echo "<script>alert('Mã nhân viên đã tồn tại. Vui lòng nhập mã nhân viên khác.');</script>";
    echo "<script>history.back();</script>"; 
} else {
    $insert_query = "INSERT INTO tblEmployee (idEmployee, name, phone, email, address) VALUES ('$idEmployee', '$name', '$phone', '$email', 'address')";
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Sinh viên đã được thêm thành công.'); window.location = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Đóng kết nối MySQL
mysqli_close($con);
?>
