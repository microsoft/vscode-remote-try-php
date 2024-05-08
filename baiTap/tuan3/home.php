<!DOCTYPE html>
<html lang="en">
  <?php 
    session_start();
    if($_SESSION["IsLogin"] == false)
      header('Location: login.php');
  ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuần 3</title>
</head>
<body>
  <?php
    include "upload.php";
  ?>
    
    <form method="POST" action="logout.php">
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-sm-5">
            <button type="submit" class="btn btn-primary">Đăng xuất</button>
          </div>
        </div>
      </form>
</body>
</html>