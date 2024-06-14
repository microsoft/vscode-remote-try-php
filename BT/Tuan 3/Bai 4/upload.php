<?php
require_once('connect.php'); // Kết nối CSDL

// Xử lý xóa tệp tin
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $file_id = $_GET['delete'];

    // Lấy thông tin tệp tin theo ID
    $sql = "SELECT * FROM files WHERE id = $file_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Xóa tệp tin khỏi ổ cứng
        $target_file = "upload/" . $row['name'];
        unlink($target_file);

        // Xóa thông tin tệp tin khỏi CSDL
        $sql = "DELETE FROM files WHERE id = $file_id";
        mysqli_query($conn, $sql);

        if (mysqli_affected_rows($conn) > 0) {
            $success = "Xóa tệp tin thành công!";
            header('Location: index.php'); // Trở về trang chủ sau khi xóa
            exit();
        } else {
            $error = "Lỗi khi xóa thông tin tệp tin.";
        }
    } else {
        $error = "Tệp tin không tồn tại.";
    }
}

// Lấy danh sách tệp tin từ CSDL (phần này có thể di chuyển vào index.php)
$sql = "SELECT * FROM files ORDER BY name ASC"; // Sắp xếp theo tên tệp tin (tăng dần)
if (isset($_GET['sort']) && $_GET['sort'] == 'date') {
    $sql = "SELECT * FROM files ORDER BY uploaded_at DESC"; // Sắp xếp theo ngày tải lên (giảm dần)
}
$result = mysqli_query($conn, $sql);
?>

**4. File `delete.php`** (trong thư mục `includes`):**

```php
<?php
// Gộp logic xóa tệp tin từ file upload.php vào đây

// Lấy ID tệp tin từ tham số GET
$file_id = $_GET['delete'];

// ... Tiếp tục logic xóa tệp tin khỏi ổ cứng và CSDL (giống như trong upload.php)
