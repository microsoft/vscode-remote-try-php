<!-- //<?php

/*----------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Licensed under the MIT License. See LICENSE in the project root for license information.
 *---------------------------------------------------------------------------------------*/

// function sayHello($name) {
// 	echo "Hello $name!";
// }

?> -->

<html>
	<head>
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
	<h2>Ví dụ 1:</h2>
		<p>Văn bản HTML.</p>
		<?php 
		
		echo '<p>Văn bản PHP!</p>';
			
		?>
		<p>Văn bản khác.</p>

		<h2>Ví dụ 2:</h2>
		<?php

			echo '<p>Khối dữ liệu PHP 1.</p>';

		?>
		<p>Dữ liệu HTML, <?php echo 'Dữ liệu PHP 2.'; ?></p>
		<?php echo'<b>'; ?>
			Một ví dụ kết hợp HTML & PHP
		<?php echo'</b>'; ?>

		<?php
			$NgonNgu1 = "PHP";
			$Ngonngu2 = "ASP .NET";
			echo $NgonNgu1, "và", $Ngonngu2, "là ngôn ngữ lập trình WEBSITE";

		?>


		<?php
			$num1 = 10;
			$num2 = 20;
			printf("%d+%d=%d",$num1,$num2,$num1+$num2 );

			define("PI", 3.14);
			$r=10;
			$s= PI * pow($r ,2);
			$p = 2 * PI * $r;
		?>

		<?php

			for($i = 1; $i <= 200; $i++){
				if ($i%2==0){
					echo'<b style = "Color: red">' . $i . '</b>';
				}
					
				else {
					echo'<i style = "Color: blue">'. $i . '</i>';
				}
					
			}
		?>
	</body>
</html>