<?php
$uploadDir = "upload/";

if (isset($_GET["filename"])) {
    $filename = $_GET["filename"];
    $filePath = $uploadDir . $filename;

    if (file_exists($filePath)) {      
        if (unlink($filePath)) {
            echo "Tệp tin " . $filename . " đã được xoá thành công.";
            echo '<br>';
            echo '<a href="index.php">Quay trở lại</a>';
        } else {
            echo "Có lỗi xảy ra khi xoá tệp tin.";
        }
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    echo "Thiếu tham số 'filename'.";
}
?>