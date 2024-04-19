<?php

/*----------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Licensed under the MIT License. See LICENSE in the project root for license information.
 *---------------------------------------------------------------------------------------*/

function func($value) {
	echo "Hello $value";
}

function func_Local() {
	$a = 5;
	echo $a;
}

function func_test() {
	static $a = 0;
	echo "Biến static ";
	echo "$a<br>";
	$a++;
}
?>

<html>
	<head>
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
	<?php 
		// tạo và in một mảng
		$array = array(1, 2, 3, 4, 5);
		print_r($array); // in ra chỉ số mảng và các giá trị tương ứng
		// phpinfo(); 
	?>
	</body>
</html>