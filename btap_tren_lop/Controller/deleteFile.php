<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['fileToDelete'])) {
        $fileToDelete = $_POST['fileToDelete'];
        if (file_exists($fileToDelete)) {
            if (unlink($fileToDelete)) {
                require_once('../config.php');
                $host = "localhost";
                $user = "root";
                $password = DB_PASSWORD;
                $dbname = "fileupload";
                $conn = new mysqli($host, $user, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $fileNameDelete = basename(pathinfo($fileToDelete, PATHINFO_FILENAME));
  
                $sql = "DELETE FROM tblfile WHERE name = '$fileNameDelete'";

                if ($conn->query($sql) === TRUE) {
                    header("Location: ../View/files.php?success=File deleted successfully.");
                    exit();
                } else {
                    header("Location: ../View/files.php?error=Failed to delete file from database.");
                    exit();
                }

                $conn->close();
            } else {
                header("Location: ../View/files.php?error=Failed to delete file from disk.");
                exit();
            }
        } else {
            header("Location: ../View/files.php?error=File does not exist.");
            exit();
        }
    } else {
        header("Location: ../View/files.php?error=Invalid file information.");
        exit();
    }
} else {
    header("Location: ../View/files.php?error=Invalid request.");
    exit();
}
?>
