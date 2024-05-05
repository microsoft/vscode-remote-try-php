<?php
// Kiểm tra xem yêu cầu có phải là POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy đường dẫn của file cần xóa từ yêu cầu POST
    $fileToDelete = $_POST['fileToDelete'];

    // Kiểm tra xem file có tồn tại không
    if (file_exists($fileToDelete)) {
        // Xóa file từ ổ đĩa
        if (unlink($fileToDelete)) {
            // Xóa file thành công
            header("Location: ../View/files.php?success=File deleted successfully.");
            exit();
        } else {
            // Xóa file thất bại
            header("Location: ../View/files.php?error=Failed to delete file.");
            exit();
        }
    } else {
        // File không tồn tại
        header("Location: ../View/files.php?error=File does not exist.");
        exit();
    }
} else {
    // Yêu cầu không hợp lệ
    header("Location: ../View/files.php?error=Invalid request.");
    exit();
}
?>
