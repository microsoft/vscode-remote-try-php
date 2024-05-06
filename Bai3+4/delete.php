<!DOCTYPE html>
<html>

<head>
    <title>Tải lên tệp</title>
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        Chọn tệp để tải lên:
        <input type="file" name="myfile" id="myfile">
        <input type="submit" value="Tải lên" name="submit">
    </form>
    <?php
    // Kết nối CSDL
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "files";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối không thành công: " . $conn->connect_error);
    }

    // Xử lý upload file
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["myfile"])) {
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["myfile"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra kích thước file
        if ($_FILES["myfile"]["size"] > 2097152) { // 2MB
            echo "File quá lớn.";
            $uploadOk = 0;
        }

        // Kiểm tra xem tệp đã tồn tại trong thư mục đích chưa
        if (file_exists($target_file)) {
            // Nếu tệp đã tồn tại, bạn có thể tạo một tên tệp mới bằng cách thêm một số ngẫu nhiên vào tên
            $filename = basename($_FILES["myfile"]["name"]);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $i = 1;
            while (file_exists($target_dir . $basename . '_' . $i . '.' . $extension)) {
                $i++;
            }
            $filename = $basename . '_' . $i . '.' . $extension;
            $target_file = $target_dir . $filename;

            // Hiển thị thông báo về việc tạo một tên tệp mới
            echo "Tệp đã tồn tại. Một tên tệp mới đã được tạo: $filename<br>";
            $uploadOk = 1;
        }

        // Cho phép chỉ tải lên các định dạng file nhất định, bạn có thể thay đổi
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Chỉ cho phép tải lên các file JPG, JPEG, PNG & GIF.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Tệp của bạn không được tải lên.";

            // Nếu mọi thứ đều ổn, cố gắng tải lên tệp
        } else {
            if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $target_file)) {
                // Lưu thông tin vào CSDL
                $filename = basename($_FILES["myfile"]["name"]);
                $filetype = $_FILES["myfile"]["type"];
                $filesize = $_FILES["myfile"]["size"];

                $sql = "INSERT INTO tblfile (filename, filetype, upload_date, filesize) VALUES ('$filename', '$filetype', NOW(), $filesize)";
                if ($conn->query($sql) === TRUE) {
                    echo "Tệp " . basename($_FILES["myfile"]["name"]) . " đã được tải lên thành công và lưu trữ trong cơ sở dữ liệu.";
                } else {
                    echo "Lỗi: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Đã xảy ra lỗi khi tải lên tệp của bạn.";
            }
        }
    }

    // Kiểm tra xem có yêu cầu xóa tệp không
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Lấy đường dẫn tệp từ CSDL
        $sql = "SELECT filename FROM tblfile WHERE id=$id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $filename = $row['filename'];

            // Xoá tệp từ ổ đĩa
            $filepath = "upload/" . $filename;
            if (unlink($filepath)) {
                // Xoá thông tin tệp từ CSDL
                $sql_delete = "DELETE FROM tblfile WHERE id=$id";
                if ($conn->query($sql_delete) === TRUE) {
                    echo "<script>alert('Tệp đã được xoá thành công.');</script>";
                } else {
                    echo "<script>alert('Lỗi khi xoá thông tin tệp từ CSDL: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('Đã xảy ra lỗi khi xoá tệp từ ổ đĩa.');</script>";
            }
        } else {
            echo "<script>alert('Không tìm thấy tệp có id = $id.');</script>";
        }
    }

    // Hiển thị danh sách các tệp đã tải lên
    $sql = "SELECT * FROM tblfile";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Danh sách các tệp đã tải lên</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Tên tệp</th><th>Loại</th><th>Ngày tải lên</th><th>Kích thước</th><th>Xóa</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["filename"] . "</td>";
            echo "<td>" . $row["filetype"] . "</td>";
            echo "<td>" . $row["upload_date"] . "</td>";
            echo "<td>" . $row["filesize"] . "</td>";
            echo "<td><a href='?id=" . $row["id"] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xoá tệp này?\")'>Xóa</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Không có tệp nào được tải lên.";
    }

    // Đóng kết nối CSDL
    $conn->close();
    ?>
</body>

</html>