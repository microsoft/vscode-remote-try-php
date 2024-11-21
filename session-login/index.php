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
    <title>Trang chủ</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Chào mừng <?php echo $_SESSION["username"]; ?> đến với trang index!</h1>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Thông tin của bạn:</h5>
            <p class="card-text">Tên đăng nhập: <?php echo $_SESSION["username"]; ?></p>
            <form class="d-flex" action="logout.php" method="post">
                <button type="submit" class="btn btn-primary">Logout</button>
                <a class="btn btn-info" style="margin-left: 10px" href="upload.php">Upload file</a>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
