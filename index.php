
<?php
/*----------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Licensed under the MIT License. See LICENSE in the project root for license information.
 *---------------------------------------------------------------------------------------*/

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
		$n = 100;
		$arr1 = array($n); // mảng chỉ có một phần tử với biến n
		echo "arr2: \n";
		print_r($arr1);
		echo "<br>";
		$arr2 = array();
		for($i = 0; $i < $n; $i++) {
			$arr2[] = "giá trị " . $i . "<br>";
		}
		echo "arr2: \n";
		print_r($arr2); // mảng có 100 phần tử với n giá trị

		echo "<br>";
		$arr3 = array(
			'hoten' => 'Nguyễn Đức Anh',
			'quequan' => 'Thanh Hóa',
			'tuoi' => '21',
			'IQ' => 'Rất cao'
		);
		echo "arr3:\n";
		print_r($arr3); // mảng kết hợp



		// phpinfo(); 
	?>
	</body>
</html>