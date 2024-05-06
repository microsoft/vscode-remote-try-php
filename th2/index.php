<!DOCTYPE html>
<html>
<head>
    <title>Quản lý tệp tin</title>
</head>
<body>
    <h1>Quản lý tệp tin</h1>

    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Tải lên" name="submit">
    </form>

    <h2>Danh sách tệp tin</h2>

    <?php
    $uploadDir = "upload/";

    // Kiểm tra sự tồn tại của thư mục upload
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir);
    }

    // Xử lý tải lên tệp tin
    if (isset($_FILES["fileToUpload"])) {
        $targetFile = $uploadDir . basename($_FILES["fileToUpload"]["name"]);
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "Tệp tin " . basename($_FILES["fileToUpload"]["name"]) . " đã được tải lên thành công.";
        } else {
            echo "Có lỗi xảy ra khi tải lên tệp tin.";
        }
    }

    // Lấy danh sách tệp tin trong thư mục upload
    $files = scandir($uploadDir);
    $files = array_diff($files, array(".", ".."));

    // Kiểm tra xem người dùng đã nhấp vào tiêu đề sắp xếp hay chưa
    $sortOrder = isset($_GET["sort"]) ? $_GET["sort"] : "none";
    if ($sortOrder == "name") {
        natsort($files); // Sắp xếp theo tên tệp
    } elseif ($sortOrder == "date") {
        $files = array_reverse($files); // Sắp xếp theo ngày tải lên (đảo ngược thứ tự)
    }

    // Hiển thị danh sách tệp tin
    if (!empty($files)) {
        echo '<table>';
        echo '<tr>';
        echo '<th><a href="index.php?sort=name">Tên tệp</a></th>';
        echo '<th><a href="index.php?sort=date">Ngày tải lên</a></th>';
        echo '<th>Loại</th>';
        echo '<th>Kích thước</th>';
        echo '</tr>';

        foreach ($files as $file) {
            $filePath = $uploadDir . $file;
            $fileType = mime_content_type($filePath);
            $fileSize = filesize($filePath);
            $fileDate = date("d/m/Y H:i:s", filemtime($filePath));

            echo '<tr>';
            echo '<td>' . $file . '</td>';
            echo '<td>' . $fileDate . '</td>';
            echo '<td>' . $fileType . '</td>';
            echo '<td>' . $fileSize . ' bytes</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo '<p>Không có tệp tin nào.</p>';
    }
    ?>
</body>
</html>