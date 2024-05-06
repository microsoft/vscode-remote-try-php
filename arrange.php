<?php
$dir = "upload/";
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
if ($sort == 'name') {
    $files = scandir($dir, SCANDIR_SORT_ASCENDING);
} elseif ($sort == 'date') {
    $files = scandir($dir, SCANDIR_SORT_DESCENDING);
} else {
    $files = scandir($dir);
}
echo "<table border='1'>";
echo "<tr><th><a href='sort.php?sort=name'>Tên tệp</a></th><th><a href='sort.php?sort=date'>Ngày tải lên</a></th><th>Loại</th><th>Kích thước</th><th>Xoá</th></tr>";
foreach($files as $file) {
    if ($file != "." && $file != "..") {
        $file_path = $dir . $file;
        echo "<tr>";
        echo "<td>".$file."</td>";
        echo "<td>".date("Y-m-d H:i:s", filemtime($file_path))."</td>";
        echo "<td>".pathinfo($file_path, PATHINFO_EXTENSION)."</td>";
        echo "<td>".filesize($file_path)." bytes</td>";
        echo "<td><a href='delete.php?file=".$file."'>Xoá</a></td>";
        echo "</tr>";
    }
}
echo "</table>";
?>