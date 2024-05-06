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

$name = mysqli_real_escape_string($con, $_POST['name']);
$phone = mysqli_real_escape_string($con, $_POST['phone']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$password = mysqli_real_escape_string($con, $_POST['password']);
$password = md5($password);

$get_last_id_query = "SELECT MAX(SUBSTRING(idCustomer, 3)) AS max_id FROM tblCustomer";
$result = mysqli_query($con, $get_last_id_query);
$row = mysqli_fetch_assoc($result);
$max_id = $row['max_id'];
$new_id = $max_id + 1;
$formatted_new_id = "KH" . str_pad($new_id, 4, '0', STR_PAD_LEFT);

$check_query = "SELECT * FROM tblCustomer WHERE name = '$name'";
$result = mysqli_query($con, $check_query);

if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Tên tài khoản đã tồn tại. Vui lòng chọn tên khác.');</script>";
    echo "<script>history.back();</script>"; 
} else {
    $insert_query = "INSERT INTO tblCustomer (idCustomer, name, phone, email, password) VALUES ('$formatted_new_id', '$name', '$phone', '$email', '$password')";
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Tạo tài khoản thành công.');</script>";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>
