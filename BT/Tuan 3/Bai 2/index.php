<?php
session_start();

// Kết nối CSDL (sử dụng file connect.php)
require_once('includes/db_config.php');

// Xử lý form tải lên tệp tin
if (isset($_POST['upload'])) {
    // Lấy dữ liệu từ form
    $target_dir = "upload/"; // Thư mục lưu trữ tệp tin
    $file_name = basename($_FILES['myfile']['name']); // Tên tệp tin
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Loại tệp tin
    $temp_file = $_FILES['myfile']['tmp_name']; // Vị trí tệp tin tạm thời

    // Kiểm tra kích thước tệp tin
    if ($_FILES['myfile']['size'] > 2097152) { // 2MB
        $error = "Kích thước tệp tin vượt quá giới hạn (2MB).";
    }

    // Kiểm tra loại tệp tin hợp lệ
    $allow_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx');
    if (!in_array($file_type, $allow_types)) {
        $error = "Chỉ hỗ trợ các loại tệp tin: " . implode(', ', $allow_types) . ".";
    }

    // Nếu không có lỗi, tiến hành tải lên tệp tin
    if (!$error) {
        // Tạo tên tệp tin mới theo định dạng: YYYYMMDD_HHMMSS_Mã_ngẫu_nhiên.ext
        $new_file_name = date('YmdHis') . '_' . uniqid() . '.' . $file_type;
        $target_file = $target_dir . $new_file_name;

        // Di chuyển tệp tin từ vị trí tạm thời vào thư mục đích
        if (move_uploaded_file($temp_file, $target_file)) {
            // Lưu thông tin tệp tin vào CSDL
            $sql = "INSERT INTO files (name, type, size, uploaded_at) VALUES ('$new_file_name', '$file_type', '" . $_FILES['myfile']['size'] . "', NOW())";
            mysqli_query($conn, $sql);

            if (mysqli_affected_rows($conn) > 0) {
                $success = "Tải lên tệp tin thành công!";
            } else {
                $error = "Lỗi khi lưu thông tin tệp tin.";
            }
        } else {
            $error = "Lỗi khi di chuyển tệp tin.";
        }
    }
}

// Lấy danh sách tệp tin từ CSDL
$sql = "SELECT * FROM files ORDER BY name ASC"; // Sắp xếp theo tên tệp tin (tăng dần)
if (isset($_GET['sort']) && $_GET['sort'] == 'date') {
    $sql = "SELECT * FROM files ORDER BY uploaded_at DESC"; // Sắp xếp theo ngày tải lên (giảm dần)
}
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tệp tin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Quản lý tệp tin</h1>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    <h2>Tải lên tệp tin</h2>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="myfile">Chọn tệp tin:</label>
        <input type="file" id="myfile" name="myfile" required>
        <button type="submit" name="upload">Tải lên</button>
    </form>

    <h2>Danh sách tệp tin</h2>
    <table>
        <thead>
            <tr>
                <th>Tên tệp tin
