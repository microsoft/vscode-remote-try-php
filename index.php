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
		// cách 1: automatic
		$var = "100" + 15;
		echo $var;
		echo "<br>";
		$var = "100" + 15.0;
		echo $var;
		echo "<br>";
		$var = 39 . " Steps";
		echo $var;

		// cách 2: (datatype)
		echo "<br>";
		$var2 = "100" + 15;
		(double) $var2;
		echo $var2;
		echo "<br>";
		// Cách 3: settype($var2, "datatype");
		settype($var2, "int");
		echo $var2;
		// phpinfo(); 
	?>
	</body>
</html>