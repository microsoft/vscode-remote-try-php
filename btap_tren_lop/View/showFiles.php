<?php
$files = scandir("../upload/");
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $file_path = "../upload/" . $file;
        if (file_exists($file_path)) {
            $file_info = pathinfo($file_path);
            $file_size = filesize($file_path);
            $file_date = date("Y-m-d H:i:s", filemtime($file_path));
            echo "<tr>";
            echo "<td>".$file_info['basename']."</td>";
            echo "<td>".$file_info['extension']."</td>";
            echo "<td>".$file_date."</td>";
            echo "<td>".$file_size." bytes</td>";
            
            // Thêm cột cho nút xóa
            echo "<td>";
            echo "<form id='deleteForm' action='../Controller/deleteFile.php' method='post' onsubmit='return confirmDelete()'>";
            echo "<input type='hidden' name='fileToDelete' value='".$file_path."'>";
            echo "<button type='submit' class='delete-btn'>Delete</button>";
            echo "</form>";
            echo "</td>";
            
            echo "</tr>";
        } else {
            
            echo "<tr><td colspan='5'>File $file không tồn tại</td></tr>";
        }
    }
}
?>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this file?");
    }
</script>
