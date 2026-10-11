<?php
if($_SESSION["IsLogin"] == false)
    header('Location: login.php');

if(isset($_POST['filename'])) {
    $filename = 'uploads/' . $_POST['filename'];

    if(file_exists($filename)){
        unlink($filename);
    }

    include "db_connect.php";
    $query = "DELETE FROM images WHERE path='$filename'";
    if ($db->query($query) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $db->error;
    }

    header("Location: home.php");
}
?>
