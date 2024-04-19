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
		$a = "Hello";
		$b = "$a world"; //tương đương $b=“Hello world”
		$c = '$a world'; //$c=‘$a world’ (không thay đổi)
		echo "a: $a<br>";
		echo "b: $b<br>";
		echo "c: $c<br>";
		// phpinfo(); 
	?>
	</body>
</html>