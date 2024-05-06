<?php
$target_dir = "../upload/";
// Lấy phần mở rộng của tệp
$extension = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);

$timestamp = date("Ymd");
// Tạo tên tệp mới bằng cách sử dụng thời gian và mã hóa sha1 của tên tệp gốc
$hashed_filename = $timestamp . '_' . sha1(basename($_FILES["fileToUpload"]["name"])) . '.' . $extension;

// Đường dẫn đến tệp đích
$target_file = $target_dir . $hashed_filename;

// Biến xác nhận tải lên
$uploadOk = 1;

$errorMessage = "";

// Kiểm tra xem tệp đã tồn tại hay chưa
if (file_exists($target_file)) {
    $errorMessage = "Sorry, file already exists.";
    $uploadOk = 0;
}

// Kiểm tra kích thước tệp
if ($_FILES["fileToUpload"]["size"] > 50000000) {
    $errorMessage = "Sorry, your file is too large.";
    $uploadOk = 0;
}


if ($uploadOk == 0) {
    header("Location: ../View/files.php?error=$errorMessage");
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Kết nối đến cơ sở dữ liệu
        require_once('../config.php');
        $host = "localhost";
        $user = "root";
        $password = DB_PASSWORD;
        $dbname = "fileupload";
        $conn = new mysqli($host, $user, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $file_name = pathinfo($hashed_filename, PATHINFO_FILENAME);
        $file_path = $target_file;
        $file_size = $_FILES["fileToUpload"]["size"];
        $file_type = $extension;

        $sql = "INSERT INTO tblfile (name, type, upload_date, size) VALUES ('$file_name', '$file_type', NOW(), '$file_size')";

        if ($conn->query($sql) === TRUE) {
   
            header("Location: ../View/files.php?success=The file $file_name has been uploaded.");
        } else {
           
            header("Location: ../View/files.php?error=Sorry, there was an error uploading your file.");
        }

        
        $conn->close();
    } else {
        
        header("Location: ../View/files.php?error=Sorry, there was an error uploading your file.");
    }
}
?>
