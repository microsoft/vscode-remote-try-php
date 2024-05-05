<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Files</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>List Files</h1>
    <a href="create-file.php" class="btn btn-info">Upload File</a>

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col" data-sort="id">ID</th>
                <th scope="col" data-sort="name">Tên tập tin</th>
                <th scope="col" data-sort="type">Loại</th>
                <th scope="col" data-sort="size">Kích thước</th>
                <th scope="col" data-sort="upload-date">Ngày tải lên</th>
                <th scope="col">Hành động</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "web_pka";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
            }

            $sql = "SELECT * FROM files";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["type"] . "</td>";
                    echo "<td>" . $row["size"] . " bytes</td>";
                    echo "<td>" . $row["upload_date"] . "</td>";
                    echo "<td><button class='btn btn-danger' onclick='deleteFile(" . $row["id"] . ")'>Xóa</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Không có tệp nào được tải lên.</td></tr>";
            }
            $conn->close();
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script> <!-- Đóng thẻ script ở đây -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function deleteFile(id) {
        if (confirm("Bạn có chắc chắn muốn xóa tệp này không?")) {
            $.ajax({
                url: 'delete-file.php',
                type: 'post',
                data: {id: id},
                success: function(response) {
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Có lỗi xảy ra khi xóa tệp.');
                    console.error(xhr.responseText);
                }
            });
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var headers = document.querySelectorAll("th[data-sort]");
        headers.forEach(function(header) {
            header.addEventListener("click", function() {
                var sortType = this.getAttribute("data-sort");
                var sortOrder = this.dataset.order || "asc";

                if (sortOrder === "asc") {
                    sortOrder = "desc";
                } else {
                    sortOrder = "asc";
                }

                headers.forEach(function(header) {
                    header.dataset.order = "";
                });
                this.dataset.order = sortOrder;
                sortTable(sortType, sortOrder);
            });
        });

        function sortTable(sortType, sortOrder) {
            var tableBody = document.querySelector("tbody");
            var rows = Array.from(tableBody.querySelectorAll("tr"));

            rows.sort(function(rowA, rowB) {
                var valueA = rowA.querySelector("." + sortType);
                var valueB = rowB.querySelector("." + sortType);

                if (sortType === "size") {
                    valueA = parseInt(valueA);
                    valueB = parseInt(valueB);
                } else if (sortType === "upload-date") {
                    valueA = new Date(valueA);
                    valueB = new Date(valueB);
                }

                if (sortOrder === "asc") {
                    return valueA > valueB ? 1 : -1;
                } else {
                    return valueA < valueB ? 1 : -1;
                }
            });

            tableBody.innerHTML = "";

            rows.forEach(function(row) {
                tableBody.appendChild(row);
            });
        }
    });
</script>


</body>
</html>

