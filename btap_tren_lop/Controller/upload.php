<?php
$target_dir = "../upload/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;

// Khởi tạo biến để lưu thông báo
$errorMessage = "";

// Kiểm tra nếu tệp đã tồn tại
if (file_exists($target_file)) {
    $errorMessage = "Sorry, file already exists.";
    $uploadOk = 0;
}

// Kiểm tra kích thước tệp
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    $errorMessage = "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Kiểm tra biến $uploadOk có bị đặt thành 0 không do có lỗi
if ($uploadOk == 0) {
    // Chuyển hướng người dùng trở lại trang index.php với thông báo lỗi
    header("Location: ../View/index.php?error=$errorMessage");
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Chuyển hướng người dùng trở lại trang index.php với thông báo tải lên thành công
        header("Location: ../View/index.php?success=The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.");
    } else {
        // Chuyển hướng người dùng trở lại trang index.php với thông báo lỗi khi tải lên
        header("Location: ../View/index.php?error=Sorry, there was an error uploading your file.");
    }
}
?>
