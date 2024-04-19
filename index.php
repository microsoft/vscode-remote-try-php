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
		define("PI","3.14");
		$r = 10;
		$s = PI * pow($r,2);
		$p = 2 * PI * $r;

		echo "R là : $r<br>";
		echo "S là : $s<br>";
		echo "P là : $p";  
		// phpinfo(); 
	?>
	</body>
</html>