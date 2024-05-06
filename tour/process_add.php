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
$startDay = mysqli_real_escape_string($con, $_POST['startDay']);
$endDay = mysqli_real_escape_string($con, $_POST['endDay']);
$cost = mysqli_real_escape_string($con, $_POST['cost']);

$get_last_id_query = "SELECT MAX(SUBSTRING(idTour, 3)) AS max_id FROM tblTour";
$result = mysqli_query($con, $get_last_id_query);
$row = mysqli_fetch_assoc($result);
$max_id = $row['max_id'];
$new_id = $max_id + 1;
$formatted_new_id = "T" . str_pad($new_id, 4, '0', STR_PAD_LEFT);

$check_query = "SELECT * FROM tblTour WHERE idTour = '$idTour'";
$result = mysqli_query($con, $check_query);

if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Tên tài khoản đã tồn tại. Vui lòng chọn tên khác.');</script>";
    echo "<script>history.back();</script>"; 
} else {
    $insert_query = "INSERT INTO tblTour (idTour, name, startDay, endDay, cost) VALUES ('$formatted_new_id', '$name', '$startDay', '$endDay', '$cost')";
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Thêm tour thành công');</script>";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>
