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
        
    </style>
</head>
<body>
    <script>
        function confirmDelete(idCustomer) {
            console.log("idCustomer: ", idCustomer);
            if (confirm('Bạn có chắc chắn muốn xóa tài khoản này không?')) {
                // Người dùng đã xác nhận xóa
                window.location.href = 'delete.php?idCustomer=' + idCustomer + '&confirm=yes';
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
    $query = mysqli_query($con, "SELECT * FROM tblCustomer WHERE idCustomer LIKE '%$search%' OR name LIKE '%$search%'");
    // Lấy tổng số tài khoản từ kết quả tìm kiếm
    $total_accounts_query = mysqli_query($con, "SELECT COUNT(*) AS total_accounts FROM tblCustomer WHERE idCustomer LIKE '%$search%' OR name LIKE '%$search%'");
} else {
    // Nếu không có yêu cầu tìm kiếm, hiển thị tất cả dữ liệu
    $query = mysqli_query($con, "SELECT * FROM tblCustomer");
    // Lấy tổng số tài khoản
    $total_accounts_query = mysqli_query($con, "SELECT COUNT(*) AS total_accounts FROM tblCustomer");
}

// Lấy tổng số tài khoản từ kết quả tìm kiếm hoặc toàn bộ dữ liệu
$total_accounts_row = mysqli_fetch_assoc($total_accounts_query);
$total_accounts = $total_accounts_row['total_accounts'];

// Kiểm tra số lượng bản ghi trả về
$rowcount = mysqli_num_rows($query);

if ($rowcount > 0) {
    echo "<h2>Danh sách tài khoản khách hàng</h2>";
    echo "<p>Tổng số tài khoản: $total_accounts</p>";
    echo "<a href='add.php' class='add-btn'>Create account</a>";
    echo "<table>";
    echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Password</th>
        <th>Action</th></tr>";

    // Lặp qua các hàng dữ liệu và hiển thị thông tin
    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>";
        echo "<td>" . $row['idCustomer'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['password'] . "</td>";
        echo "<td>";
        echo "<a href='#' class='delete-btn'> <i class='fa-solid fa-up-right-from-square fa-sm' style='color: #0288d1;'></i></a>";
        echo "<a href='delete.php?idCustomer=" . $row['idCustomer'] . "' class='delete-btn'><i class='fa-solid fa-file-pen fa-sm' style='color: #0288d1;'></i></a>";
        echo "<a href='javascript:void(0);' class='delete-btn' onclick='confirmDelete(\"" . $row['idCustomer'] . "\")'><i class='fa-solid fa-trash fa-sm' style='color: #f0284a;'></i></a>";
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
</html>
