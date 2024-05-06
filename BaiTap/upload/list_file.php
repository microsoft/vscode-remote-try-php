<?php
session_start();
$uploadDirectory = "upload/";

// Kiểm tra xem thư mục upload tồn tại không
if (is_dir($uploadDirectory)) {
    // Lấy danh sách các tệp từ thư mục upload
    $files = scandir($uploadDirectory);

    // Bỏ qua "." và ".."
    $files = array_diff($files, array('.', '..'));

    // Kiểm tra nếu người dùng nhấp vào tiêu đề sắp xếp
    if (isset($_GET['sort'])) {
        $sort = $_GET['sort'];
        
        // Xác định trạng thái sắp xếp hiện tại
        $currentSort = isset($_SESSION['sort']) ? $_SESSION['sort'] : '';

        // Xác định trạng thái sắp xếp tiếp theo
        $nextSort = ($currentSort == $sort && isset($_SESSION['order']) && $_SESSION['order'] == 'asc') ? 'desc' : 'asc';
        
        // Lưu trạng thái sắp xếp tiếp theo vào session
        $_SESSION['sort'] = $sort;
        $_SESSION['order'] = $nextSort;

        switch ($sort) {
            case 'name':
                // Sắp xếp theo tên tệp
                sort($files);
                break;
            case 'date':
                // Sắp xếp theo ngày tải lên
                usort($files, function($a, $b) use ($uploadDirectory) {
                    return filemtime($uploadDirectory . $b) - filemtime($uploadDirectory . $a);
                });
                break;
            case 'size':
                // Sắp xếp theo kích thước tệp
                usort($files, function($a, $b) use ($uploadDirectory) {
                    return filesize($uploadDirectory . $b) - filesize($uploadDirectory . $a);
                });
                break;
        }

        // Nếu trạng thái sắp xếp là giảm dần, đảo ngược danh sách
        if ($nextSort == 'desc') {
            $files = array_reverse($files);
        }
    }

    // Hiển thị bảng thông tin chi tiết của các tệp
    echo "<table border='1'>
            <tr>
                <th><a href='?sort=name'>Tên tệp</a></th>
                <th>Loại</th>
                <th><a href='?sort=date'>Ngày tải lên</a></th>
                <th><a href='?sort=size'>Kích thước</a></th>
                <th><a>Xóa</a></th>
            </tr>";

    foreach ($files as $file) {
        $filePath = $uploadDirectory . $file;
        $fileType = mime_content_type($filePath);
        $fileSize = filesize($filePath);
        $fileDate = date("d-m-Y H:i:s", filemtime($filePath));

        echo "<tr>";
        echo "<td>$file</td>";
        echo "<td>$fileType</td>";
        echo "<td>$fileDate</td>";
        echo "<td>$fileSize bytes</td>";
        echo "<td><a href='delete.php?file=$file'>Xóa</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Thư mục upload không tồn tại hoặc không thể truy cập.";
}


?>
<style>
    table{
        width: 70%;
        background-color: while;
        color: black;
    }
    tr{
        height: 30px;
    }
</style>