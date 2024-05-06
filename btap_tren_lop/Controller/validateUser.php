<?php
    session_start();

    $tk = $_REQUEST["txtTenTaiKhoan"];
    $mk = $_REQUEST["txtMatKhau"];

    require_once('../config.php'); 
    $host = "localhost";
    $user = "root";
    $password = DB_PASSWORD;

    $con = mysqli_connect($host, $user, $password);

    if (!$con) {
        die("Kết nối không thành công: " . mysqli_connect_error());
    }

    $database_name = "sinhvien";
    if (!mysqli_select_db($con, $database_name)) {
        die("Không thể chọn cơ sở dữ liệu '$database_name': " . mysqli_error($con));
    }

    $sql = "SELECT * FROM tblusers WHERE TK = '$tk' AND MK = '$mk'";

    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Xác thực thành công, thiết lập cookie
        $sessionId = session_id();
        setcookie('sessionId', $sessionId, time() + (86400), "/"); // Hết hạn trong 1 ngày (86400 giây)

        $_SESSION['IsLogin'] = true;
        header("Location: ../View/content.php");
        exit();
    } else {
        echo "User or password incorrect";
        header("Refresh:2; url=../View/login.html"); 
        exit();
    }
    mysqli_close($con);
?>
