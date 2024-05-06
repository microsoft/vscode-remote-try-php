<?php
$target_dir = "../upload/";
$extension = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);
$timestamp = date("Ymd");
$hashed_filename = $timestamp . '_' . sha1(basename($_FILES["fileToUpload"]["name"])) . '.' . $extension;
$target_file = $target_dir . $hashed_filename;
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
    header("Location: ../View/files.php?error=$errorMessage");
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Chuyển hướng người dùng trở lại trang index.php với thông báo tải lên thành công
        header("Location: ../View/files.php?success=The file ". basename($hashed_filename). " has been uploaded.");
    } else {
        // Chuyển hướng người dùng trở lại trang index.php với thông báo lỗi khi tải lên
        header("Location: ../View/files.php?error=Sorry, there was an error uploading your file.");
    }
}
?>
