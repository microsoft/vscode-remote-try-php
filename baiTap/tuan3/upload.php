<form action="upload.php" method="post" enctype="multipart/form-data">
    Select file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
</form>
<form action="home.php">
    <input type="submit" value="Danh sách file">
</form>
<?php
if(isset($_POST["submit"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
        // Kết nối với CSDL
        $db = new mysqli('localhost', 'root', '', 'db_test');
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }
        // Lưu đường dẫn tới ảnh vào CSDL
        $query = "INSERT INTO images (path) VALUES ('$target_file')";
        if ($db->query($query) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $query . "<br>" . $db->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
