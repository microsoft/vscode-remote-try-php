<form method="POST" action="logged_in.php">
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
