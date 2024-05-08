<!DOCTYPE html>
<html lang="en">
<?php 
  session_start();
  if($_SESSION["IsLogin"] == true) 
    header("Location: home.php");
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <form method="POST" action="validateuser.php">
        <div class="row mb-3"> 
          <label for="inputUsername" class="col-sm-5 col-form-label">Tên đăng nhập:</label>
          <div class="col-sm-7">
            <input type="text" name="username" class="form-control" id="inputUsername" required>
          </div>
        </div>
        <div class="row mb-3">
          <label for="inputPassword" class="col-sm-5 col-form-label">Mật khẩu:</label>
          <div class="col-sm-7">
            <input type="password" name="password" class="form-control" id="inputPassword" required>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2"></div>
          <div class="col-sm-5">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
      <?php
        if(isset($_GET['error'])) {
            echo '<script>alert("' . $_GET['error'] . '");</script>';
        }
      ?>
</body>
</html>