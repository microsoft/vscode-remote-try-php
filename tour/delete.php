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

if (isset($_GET['idTour'])) {
    $idTour = $_GET['idTour'];
    
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $delete_query = "DELETE FROM tblTour WHERE idTour = '$idTour'";

        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('Tour đã được xóa thành công.'); window.location = 'index.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa tour: " . mysqli_error($con) . "');</script>";
        }        
    }
}
// Đóng kết nối
mysqli_close($con);
?>
