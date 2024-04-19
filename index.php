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
		$don_gia = 5000;
		$so_luong = 100;
		$thanh_tien = (double)($so_luong*$don_gia);
		echo $thanh_tien;
		// phpinfo(); 
	?>
	</body>
</html>