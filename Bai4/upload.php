<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tải lên tệp</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
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

            // Lấy thông tin về tệp
            $filename = $_FILES["myfile"]["name"];
            $filesize = $_FILES["myfile"]["size"];
            $filetype = $_FILES["myfile"]["type"];

            // Tạo chuỗi mã băm từ tên tệp
            $hash = md5($filename);
            // Lấy phần mở rộng của tệp
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            // Đặt lại tên tệp mới kèm mã hoá và ngày hiện tại
            $newFilename = date('Ymd') . '_' . $hash . '.' . $extension;

            $target_file = $target_dir . $newFilename;
            $uploadOk = 1;

            // Kiểm tra kích thước file
            if ($filesize > 2097152) { // 2MB
                echo "File quá lớn.";
                $uploadOk = 0;
            }

            // Kiểm tra xem tệp đã tồn tại chưa
            if (file_exists($target_file)) {
                echo "Tệp đã tồn tại.";
                $uploadOk = 0;
            }

            // Cho phép chỉ tải lên các định dạng file nhất định
            if (
                $extension != "jpg" && $extension != "png" && $extension != "jpeg"
                && $extension != "gif"
            ) {
                echo "Chỉ cho phép tải lên các file JPG, JPEG, PNG & GIF.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Tệp của bạn không được tải lên.";
            } else {
                if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $target_file)) {
                    // Lưu thông tin vào CSDL
                    $sql = "INSERT INTO tblfile (filename, filetype, upload_date, filesize) VALUES ('$newFilename', '$filetype', NOW(), $filesize)";
                    if ($conn->query($sql) === TRUE) {
                        echo "Tệp " . $filename . " đã được tải lên thành công và lưu trữ trong cơ sở dữ liệu.";
                    } else {
                        echo "Lỗi: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    echo "Đã xảy ra lỗi khi tải lên tệp của bạn.";
                }
            }
        }
        ?>

        <div class="file-list">
            <?php
            // Hiển thị danh sách các tệp đã tải lên $sql="SELECT * FROM tblfile" ; $result=$conn->query($sql);
            $sql = "SELECT * FROM tblfile";
            $result = $conn->query($sql);
            if ($result !== false && $result->num_rows > 0) {
                echo "<h2>Danh sách các tệp đã tải lên</h2>";
                echo "<table border='1'>";
                echo "<tr>
                    <th>Tên tệp</th>
                    <th>Loại</th>
                    <th>Ngày tải lên</th>
                    <th>Kích thước</th>
                    <th>Xóa</th>
                </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["filename"] . "</td>";
                    echo "<td>" . $row["filetype"] . "</td>";
                    echo "<td>" . $row["upload_date"] . "</td>";
                    echo "<td>" . $row["filesize"] . "</td>";
                    echo "<td><a href='delete.php?id=" . $row["id"] . "'>Xóa</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Không có tệp nào được tải lên.";
            }

            // Đóng kết nối CSDL
            $conn->close();
            ?>
        </div>
    </div>
</body>

</html>