<!DOCTYPE html>
<html>
<head>
    <title>Danh sách sách</title>
</head>
<body>
    <h1>Danh sách sách</h1>
    <table border="1">
        <tr>
            <th>Mã sách</th>
            <th>Tên sách</th>
            <th>Số lượng</th>
        </tr>
        <?php foreach ($sachList as $sach): ?>
        <tr>
            <td><?php echo $sach['MaSach']; ?></td>
            <td><?php echo $sach['TenSach']; ?></td>
            <td><?php echo $sach['SoLuong']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
