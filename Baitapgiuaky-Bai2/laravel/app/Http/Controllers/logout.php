<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: ../../../resources/views/HelloWorld.blade.php");
    exit();
?>