<?php
    session_start();
    $_SESSION["IsLogin"] = false;
    header("Location: login.php");
?>
