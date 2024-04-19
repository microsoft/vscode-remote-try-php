<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <label for="Số lượng">Nhập số lượng:</label>
        <input type="text" name="quantity" id="quantity">
        <label for="Đơn giá">Nhập giá:</label>
        <input type="text" name="price" id="price">
        <input type="submit" value="Result">
    </form>
    <?php 
        if(isset($_POST['quantity']) && isset($_POST['price'])) {
            $so_luong = $_POST['quantity'];
            $don_gia = $_POST['price'];

            if ($so_luong < 10)
                $thanh_tien = $so_luong * $don_gia;
            elseif ($so_luong >= 10 && $so_luong <= 20)
                $thanh_tien = ($so_luong * $don_gia) * 0.95;
            else
                $thanh_tien = ($so_luong * $don_gia) * 0.9;

            echo "<p>Total Price: $thanh_tien</p>";
        }
    ?>
</body>
</html>