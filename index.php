

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
		<h2>Nhóm 17</h2>
		<?php
		for ($i = 1; $i <= 200; $i++) {
			if ($i % 2 == 0) {
				echo '<span style="color: red; font-weight: bold;">' . $i . '</span> ';
			} else {
				echo '<span style="color: blue; font-style: italic;">' . $i . '</span> ';
			}
		}
		?>
		<div>
			<h1>Đăng nhập hệ thống</h1>
			<form action="info.php" method="get">
				<label for="username">Tên đăng nhập : </label>
				<input type="text" name="username"> <br><br>
				<label for="password">Mật khẩu:</label>
				<input type="password" name="password"><br><br>
				<input type="submit">
			</form>
		</div>
		
	</body>
</html>