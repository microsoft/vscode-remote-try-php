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
		<!-- Biến cục bộ -->
		<?php
		function Test()
			{
			$a=5;
			echo $a; // phạm vi cục bộ
			}
			Test();
			echo $a;
		?>

		<br>
		<!-- Biến toàn cục -->
		<?php
			$a = 1;
			$b = 2;
			function Sum1()
			{
				global $a, $b;
				$b = $a + $b;
			}
			Sum1();
			echo $b; 
		?>
		<!-- Biến static -->
		<br>
		<?php
			function Test1()
			{
				static $a = 0;
				echo $a;
				$a++;
			}
			Test1(); 
			Test1();
			Test1();
		?>
		<!-- Biến toàn cục -->
		<?php
			$a = 1;
			$b = 2;
			function Sum()
			{
				global $a, $b;
				$b = $a + $b;
			}
			Sum();
			echo $b;
		?>
		<br>
	</body>
</html>