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
		$a = 1234; // hệ thập phân
		$b = -123; // số âm hệ thập phân
		$c = 0123; // hệ bát phân (bắt đầu bằng 0 theo sau là các ký số)
		$d = 0x1A; // hệ thập lục phân (bắt đầu bằng 0x theo sau là các ký số)
		echo "a: $a<br>";
		echo "b: $b<br>";
		echo "c: $c<br>";
		echo "d: $d<br>";
		// phpinfo(); 
	?>
	</body>
</html>