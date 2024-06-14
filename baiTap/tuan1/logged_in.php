<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Web n�ng cao</title>

</head>

<style>
  body {
    width: 350px;
  }
</style>

<body>
	
<form>
  <div class="row mb-3"> 
    <label class="col-sm-5 col-form-label">Tên đăng nhập là:</label>
    <label class="col-sm-5 col-form-label">
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST["username"];
                echo $username;
            }
        ?>
    </label>
  </div>
  <div class="row mb-3">
    <label class="col-sm-5 col-form-label">Mật khẩu là:</label>
    <label class="col-sm-5 col-form-label">
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $password = $_POST["password"];
                echo $password;
            }
        ?>
    </label>
  </div>
</form>

</body>

</html>