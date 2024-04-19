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
		// Trong PHP tham chiếu có nghĩa là lấy cùng một giá trị bằng nhiều tên biến khác nhau.
		// Ký hiệu tham chiếu: &
		$a = 10;
		$b = &$a;
		//take value
		echo $a; 
		echo $b;
		// phpinfo(); 
	?>
	</body>
</html>