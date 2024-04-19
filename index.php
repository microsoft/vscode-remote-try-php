<?php
// Xử lý biểu mẫu tìm kiếm khi được gửi đi
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["txtTukhoa"])) {
    $sTukhoa = htmlspecialchars($_GET["txtTukhoa"]);
    $searched = true;
} else {
    $searched = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm sách</title>
</head>
<body>
    <h1>TÌM SÁCH</h1>

    <?php if ($searched) { ?>
        <p>Từ khóa tìm sách là: <?= $sTukhoa ?></p>
        <p>Kết quả tìm là:</p>
        <ul>
            <!-- Thêm logic hiển thị kết quả tìm kiếm ở đây -->
        </ul>
    <?php } else { ?>
        <form action="index.php" method="GET">
            <label for="txtTukhoa">Từ khóa:</label>
            <input type="text" id="txtTukhoa" name="txtTukhoa">
            <input type="submit" value="Tìm">
        </form>
    <?php } ?>
</body>
</html>
