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
		<p>dữ liệu HTML, <?php echo 'Văn bản PHP!'; ?></p>
		<?php echo'<b>'; ?>
			Một ví dụ kết hợp HTML & PHP
		<?php echo'</b>'; ?>
	</body>
</html>