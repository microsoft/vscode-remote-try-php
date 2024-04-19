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
		
		<p>Văn bản HTML.</p>
		<?php 
			echo '<p>Văn bản PHP</p>'
		?>
		<p>Văn bản HTML khác</p>

		<?php 
			echo '<p>Khối dữ liệu PHP1</p>'
		?>
		<p>Dữ liệu HTML, <?php echo 'Dữ liệu PHP2' ?></p>
		<?php echo '<b>'; ?>
			Một ví dụ kết hợp HTNL và PHP
		<?php echo '</b>'; ?>
		<br><br>
		<form action="xltimsach.php" Method="GET" >
        Từ khóa : <input type="text" name="txtTukhoa"/>
        <input type="submit" value="Tìm"/>
        </form>
		<br><br>
		<?php
			define("PI", 3.14);
			$r=10;
			$s = PI * pow($r,2);
			$p = 2*PI * $r;
			printf("$%.2f", $s);
		?>

	</body>
</html>