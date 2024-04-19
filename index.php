<?php

/*----------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Licensed under the MIT License. See LICENSE in the project root for license information.
 *---------------------------------------------------------------------------------------*/

function sayHello($name) {
	echo "Hello $name!";
}

?>

<html>
	<head>
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
		<p>Văn bản HTML.</p>
		<?php
			echo '<p>Văn bản PHP!</p>'
		?>
		<p>Văn bản HTML khác.</p>

		<?php
			echo '<p>Khối dữ liệu PHP 1.</p>';
		?>
		<p>Dữ liệu HTML, <?php echo 'Dữ liệu PHP 2.'; ?></p>

		<?php echo '<b>'; ?>
			Một ví dụ kết hợp HTML và PHP.
		<?php echo '<b>'; ?>

		<h1>Tìm sách</h1>
		<form action="test.php" Method="GET">
			Từ khóa : <input type="text" name="txtTukhoa"/>
			<input type="submit" value="Tìm"/>
		</form>


		<p>Trần Duy Bim</p>
	</body>
</html>