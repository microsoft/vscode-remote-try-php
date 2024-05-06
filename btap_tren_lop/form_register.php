<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Đăng ký tài khoản</h1>

    <form id="registrationForm" action="result_register.php" method="POST">
        <label for="txtMSSV">MSSV:</label>
        <input type="text" id="txtMSSV" name="txtMSSV" required>
        <br><br>
        <label for="txtHoTen">Họ và tên:</label>
        <input type="text" id="txtHoTen" name="txtHoTen" required>
        <br><br>
        <label for="txtLop">Lớp:</label>
        <input type="text" id="txtLop" name="txtLop" required>
        <br><br>
        <label for="txtKhoa">Khoa:</label>
        <input type="text" id="txtKhoa" name="txtKhoa" required>
        <br><br>
        <label for="txtSDT">SDT:</label>
        <input type="text" id="txtSDT" name="txtSDT" required>
        <br><br>
        <label for="txtTK">Tài khoản:</label>
        <input type="text" id="txtTK" name="txtTK" required>
        <br><br>
        <label for="txtMatKhau">Mật khẩu:</label>
        <input type="password" id="txtMatKhau" name="txtMatKhau" required>
        <br><br>
        <input type="submit" value="Đăng ký">
    </form>

    <script>
        function confirmRegistration() {
            if (confirm("Đăng ký thành công! Bạn có muốn chuyển đến trang đăng nhập?")) {
                window.location.href = "form_login.php"; // Chuyển hướng đến trang login
            } else {
                return false; // Ngăn form submit mặc định nếu người dùng nhấn "Cancel"
            }
        }
    </script>
    </body>
</html>
