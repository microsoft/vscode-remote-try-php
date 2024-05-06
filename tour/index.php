<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;0,900;1,300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dc2a236791.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../img/fa.ico" type="image/x-icon">
    <title>Thông tin Sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            /* align-items: center; */
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: navy;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td a {
            text-decoration: none;
            color: blue;
            margin-right: 8px;
        }
        td a.delete-btn {
            display: inline-block;
            padding: 4px 6px;
            /* background-color: rgba(0, 0, 0.1, 0.1); */
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
        }
        td a.delete-btn:hover {
            background-color: #0288d10a;
        }
        td a.edit-btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 8px;
        }
        td a.edit-btn:hover {
            background-color: #45a049;
        }

        a.add-btn {
            display: block;
            width: 120px;
            padding: 8px 12px;
            background-color: #0056b3; /* Màu xanh của Bootstrap */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px; /* Khoảng cách giữa nút và bảng */
            text-align: center;
        }
        a.add-btn:hover {
            background-color: #0056b3; /* Màu xanh lá cây nhạt khi di chuột vào */
        }
        tr:hover {
            background-color: #f6f6f6; /* Red background on hover */
        }

        *{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: 'Roboto', sans-serif; 
}

a{
    color: white;
    text-decoration: none;
}

body{
    width: 1519px;
}



.top-header{
    height: 50px;
    display: flex;
    /* padding: 0 145px; */
    justify-content:space-between;
    color: #333333;
    line-height: 25.6px;
    background-color: #ededed;
}

.top-header-title{
    padding-left: 15px;
}

.top-header-list{
    padding-right: 15px;
}

.top-header-list>ul>li>a:hover{
    color: red;
}

.top-header-list ul{
    align-items: center;
    color: #333333;
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    line-height: 25.6px;
    list-style: none;
}

.top-header-list ul li{
    height: 15px;
    margin-top:5px;
    padding: 0 10px;
    border-right: 1px solid #ccc;
}

.top-header-list ul li:last-child{
    border: none;
}

.top-header-title a{
    color: #484848;
    font-family: 'Roboto', sans-serif;
    font-size: 13px;
    font-weight: 700;
    line-height: 22.4px;
    text-align: left;
}

.top-header-list a{
    height: 20px;
    padding: 8 0px;
    color:#4d4646;
    flex-wrap:wrap;
    font-family: 'Open Sans', sans-serif;
    font-size: 14px;
    line-height: 22.4px;
    text-align:left;

}
.cost-column {
    text-align: right;
}
        
    </style>
</head>
<body>
    <script>
        function confirmDelete(idTour) {
            console.log("idTour: ", idTour);
            if (confirm('Bạn có chắc chắn muốn tour này không?')) {
                // Người dùng đã xác nhận xóa
                window.location.href = 'delete.php?idTour=' + idTour + '&confirm=yes';
            }
        }
    </script>
     <script>
        function confirmEdit(idTour) {
            console.log("idTour: ", idTour);
            if (confirm('Bạn có muốn sửa tour này không?')) {
                // Người dùng đã xác nhận xóa
                window.location.href = 'edit.php?idTour=' + idTour + '&confirm=yes';
            }
        }
    </script>
    <script>
        function goToIndex() {
                window.location.href = 'index.php';
        }
    </script>
    
    <div class="container">
    <form action="" method="GET">
        <input type="text" name="search" placeholder="Search by ID or Name">
        <button type="submit">Search</button>
    </form>

    <?php
// Kết nối đến cơ sở dữ liệu
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "travelEasy";
$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Xử lý yêu cầu tìm kiếm nếu tồn tại
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = mysqli_real_escape_string($con, $_GET['search']);
    // Truy vấn SQL để tìm kiếm theo idCustomer hoặc name
    $query = mysqli_query($con, "SELECT * FROM tblTour WHERE idTour LIKE '%$search%' OR name LIKE '%$search%'");
    // Lấy tổng số tài khoản từ kết quả tìm kiếm
    $total_accounts_query = mysqli_query($con, "SELECT COUNT(*) AS total_accounts FROM tblTour WHERE idTour LIKE '%$search%' OR name LIKE '%$search%'");
} else {
    // Nếu không có yêu cầu tìm kiếm, hiển thị tất cả dữ liệu
    $query = mysqli_query($con, "SELECT * FROM tblTour");
    // Lấy tổng số tài khoản
    $total_accounts_query = mysqli_query($con, "SELECT COUNT(*) AS total_accounts FROM tblTour");
}

// Lấy tổng số tài khoản từ kết quả tìm kiếm hoặc toàn bộ dữ liệu
$total_accounts_row = mysqli_fetch_assoc($total_accounts_query);
$total_accounts = $total_accounts_row['total_accounts'];

// Kiểm tra số lượng bản ghi trả về
$rowcount = mysqli_num_rows($query);

if ($rowcount > 0) {
    echo "<h2>Danh sách tour</h2>";
    echo "<p>Tổng số tour: $total_accounts</p>";
    echo "<a href='add.php' class='add-btn'>Add Tour</a>";
    echo "<table>";
    echo "<tr>
        <th>Mã Tour</th>
        <th>Tên Tour</th>
        <th>Ngày bắt đầu</th>
        <th>Ngày kết thúc</th>
        <th>Chi Phí</th>
        <th>Action</th></tr>";

    // Lặp qua các hàng dữ liệu và hiển thị thông tin
    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>" . $row['idTour'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['startDay'] . "</td>";
        echo "<td>" . $row['endDay'] . "</td>";
        // echo "<td>" . $row['cost'] . "</td>";
        // echo "<td>" . number_format($row['cost']) . "</td>";
        echo "<td class='cost-column'>" . number_format($row['cost'], 0, ',', '.' ) . "</td>";

        echo "<td>";
        echo "<a href='#' class='delete-btn'> <i class='fa-solid fa-up-right-from-square fa-sm' style='color: #0288d1;'></i></a>";
        echo "<a href='edit.php?idTour=" . $row['idTour'] . "' class='delete-btn' onclick='confirmEdit(\"" . $row['idTour'] . "\")'><i class='fa-solid fa-file-pen fa-sm' style='color: #0288d1;'></i></a>";
        echo "<a href='javascript:void(0);' class='delete-btn' onclick='confirmDelete(\"" . $row['idTour'] . "\")'><i class='fa-solid fa-trash fa-sm' style='color: #f0284a;'></i></a>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<h2>Danh sách tài khoản khách hàng</h2>";
    echo "<p>Tổng số tài khoản: $total_accounts</p>"; // Hiển thị tổng số tài khoản
    echo "<a href='add.php' class='add-btn'>Create account</a>";
}

// Đóng kết nối
mysqli_close($con);
?>


    </div>
    
</body>
</html> -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;0,900;1,300&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dc2a236791.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../img/fa.ico" type="image/x-icon">
    <title>Thông tin Sinh viên</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: navy;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td a {
            text-decoration: none;
            color: blue;
            margin-right: 8px;
        }
        td a.delete-btn {
            display: inline-block;
            padding: 4px 6px;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
        }
        td a.delete-btn:hover {
            background-color: #0288d10a;
        }
        td a.edit-btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 8px;
        }
        td a.edit-btn:hover {
            background-color: #45a049;
        }

        a.add-btn {
            display: block;
            width: 120px;
            padding: 8px 12px;
            background-color: #0056b3;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            text-align: center;
        }
        a.add-btn:hover {
            background-color: #0056b3;
        }
        tr:hover {
            background-color: #f6f6f6;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        a {
            color: white;
            text-decoration: none;
        }

        body {
            width: 1519px;
        }

        .search-form {
            float: right;
            margin-bottom: 20px;
        }

        .search-form input[type="text"],
        .search-form input[type="date"],
        .search-form input[type="number"] {
            margin-right: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form button {
            padding: 8px 12px;
            background-color: #0056b3;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #00478f;
        }

        .cost-column {
            text-align: right;
        }

    </style>
</head>
<body>
    <script>
        function confirmDelete(idTour) {
            // console.log("idTour: ", idTour);
            // if (confirm('Bạn có chắc chắn muốn tour này không?')) {
                // Người dùng đã xác nhận xóa
                window.location.href = 'delete.php?idTour=' + idTour + '&confirm=yes';
            // }
        }
    </script>
    <div class="container">
        <div class="search-form">
            <form action="" method="GET">
                <input type="text" name="idTour" placeholder="ID Tour">
                <input type="text" name="name" placeholder="Tên Tour">
                <input type="date" name="startDay" placeholder="Ngày bắt đầu">
                <input type="date" name="endDay" placeholder="Ngày kết thúc">
                <input type="number" name="cost" placeholder="Chi Phí">
                <button type="submit" name="apply">Áp dụng</button>
            </form>
        </div>

        <?php
        $host = "localhost";
        $user = "root";
        $password = "ducanh12@#";
        $database = "travelEasy";
        $con = mysqli_connect($host, $user, $password, $database);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        // $query = mysqli_query($con, "SELECT tblTour.idTour, tblTour.name AS tour_name, tblTour.startDay, tblTour.endDay, tblTour.cost, tblHotel.name AS hotel_name, tblCity.name AS City, tblDistrict.name AS District, tblWard.name AS Ward FROM tblTour 
        // JOIN tblHotel ON tblTour.idHotel = tblHotel.idHotel
        // JOIN tblAddress ON tblTour.idAddress= tblAddress.idAddress
        // JOIN tblCity ON tblAddress.idCity = tblCity.idCity
        // JOIN tblDistrict ON tblDistrict.idCity = tblCity.idCity
        // JOIN tblWard ON tblWard.idDistrict = tblDistrict.idDistrict
        // ");
        $query = mysqli_query($con, "SELECT * FROM tblTour 
        ");
        if (isset($_GET['apply'])) {
            $conditions = array();

            if (!empty($_GET['idTour'])) {
                $conditions[] = "idTour LIKE '%" . mysqli_real_escape_string($con, $_GET['idTour']) . "%'";
            }
            if (!empty($_GET['name'])) {
                $conditions[] = "name LIKE '%" . mysqli_real_escape_string($con, $_GET['name']) . "%'";
            }
            if (!empty($_GET['startDay'])) {
                $conditions[] = "startDay = '" . mysqli_real_escape_string($con, $_GET['startDay']) . "'";
            }
            if (!empty($_GET['endDay'])) {
                $conditions[] = "endDay = '" . mysqli_real_escape_string($con, $_GET['endDay']) . "'";
            }
            if (!empty($_GET['cost'])) {
                $conditions[] = "cost = " . mysqli_real_escape_string($con, $_GET['cost']);
            }
        

            $query_condition = implode(" AND ", $conditions);
            // $query = "SELECT tblTour.idTour, tblTour.name AS tour_name, tblTour.startDay, tblTour.endDay, tblTour.cost, tblHotel.name AS hotel_name, tblCity.name AS City, tblDistrict.name AS District, tblWard.name AS Ward FROM tblTour 
            // JOIN tblHotel ON tblTour.idHotel = tblHotel.idHotel
            // JOIN tblAddress ON tblTour.idAddress= tblAddress.idAddress
            // JOIN tblCity ON tblAddress.idCity = tblCity.idCity
            // JOIN tblDistrict ON tblDistrict.idCity = tblCity.idCity
            // JOIN tblWard ON tblWard.idDistrict = tblDistrict.idDistrict
            // ";
            $query = "SELECT * FROM tblTour 
            ";
            if (!empty($query_condition)) {
                $query .= " WHERE " . $query_condition;
            }
            // <th>Hotel</th>
            // <th>City</th>
            // <th>District</th>
            // <th>Ward</th>

            // Thực hiện truy vấn và hiển thị kết quả
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                echo "<h2>Danh sách tour</h2>";
                // echo "<p>Tổng số tour: $total_accounts</p>";
                echo "<a href='add.php' class='add-btn'>Add Tour</a>";
                echo "<table>";
                echo "<tr>
                    <th>Mã Tour</th>
                    <th>Tên Tour</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Chi Phí</th>
                   
                    <th>Action</th></tr>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['idTour'] . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['startDay'] . "</td>";
                    echo "<td>" . $row['endDay'] . "</td>";
                    echo "<td class='cost-column'>" . number_format($row['cost'], 0, ',', '.') . "</td>";
                    // echo "<td>" . $row['hotel_name'] . "</td>";
                    // echo "<td>" . $row['City'] . "</td>";
                    // echo "<td>" . $row['District'] . "</td>";
                    // echo "<td>" . $row['Ward'] . "</td>";
                    echo "<td>";
                    // echo "<a href='#' class='delete-btn'> <i class='fa-solid fa-up-right-from-square fa-sm' style='color: #0288d1;'></i></a>";
                    echo "<a href='view_tour.php?idTour=" . $row['idTour'] . "' class='delete-btn'><i class='fa-solid fa-up-right-from-square fa-sm' style='color: #0288d1;'></i></a>";
                    echo "<a href='edit.php?idTour=" . $row['idTour'] . "' class='delete-btn'><i class='fa-solid fa-file-pen fa-sm' style='color: #0288d1;'></i></a>";
                    echo "<a href='javascript:void(0);' class='delete-btn' onclick='confirmDelete(\"" . $row['idTour'] . "\")'><i class='fa-solid fa-trash fa-sm' style='color: #f0284a;'></i></a>";                    echo "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                // echo "<p>Không tìm thấy kết quả phù hợp.</p>";
                echo "<h2>Danh sách tour</h2>";
                echo "<a href='add.php' class='add-btn'>Add tour</a>";
            }
        }
        ?>

    </div>
</body>
</html>
