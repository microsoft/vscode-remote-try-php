<?php
if($_SESSION["IsLogin"] == false)
    header('Location: login.php');
?>

<form action="upload.php" method="POST" enctype="multipart/form-data">
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

        include "db_connect.php";
        $result = $db->query("SELECT MAX(id) AS max_id FROM images");

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $max_id = $row['max_id'];

            if (isset($max_id)) {
                $_SESSION['index'] = $max_id + 1;
            } else {
                $_SESSION['index'] = 1;
            }
        } else {
            $_SESSION['index'] = 1;
        }
        $id = $_SESSION['index'];
        $query = "INSERT INTO images (id, path) VALUES ('$id', '$target_file')";
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
