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
		$chuoi_1 = "String 1";
		$chuoi_2 = " String 2";
		$chuoi = $chuoi_1 . $chuoi_2; //nối 2 chuỗi
		echo $chuoi;
		// phpinfo(); 
	?>
	</body>
</html>