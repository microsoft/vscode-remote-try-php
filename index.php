

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
		<h2>BT1</h2>
		<!-- <?php
		for ($i = 1; $i <= 200; $i++) {
			if ($i % 2 == 0) {
				echo '<span style="color: red; font-weight: bold;">' . $i . '</span> ';
			} else {
				echo '<span style="color: blue; font-style: italic;">' . $i . '</span> ';
			}
		}
		?> -->
		<!-- <div>
			<?php
				$Ngonngu1="PHP";
				$Ngonngu2="ASP.NET";
				echo "<p>$Ngonngu1 và $Ngonngu2 là ngôn ngữ WebServer.</p>";
				print "<p>$Ngonngu1 và $Ngonngu2 là ngôn ngữ WebServer.</p>";
				printf("%s và %s là ngôn ngữ WebServer",$Ngonngu1,$Ngonngu2);
			?>
		</div> -->
		<div>
			<?php
				$n = 100;
				$arr1 = array($n);
				$arr2 = array(1,2,3,4);
				$arr3 = array();
				$arr4 = array("hoten" => "Tiến Đức",
							"quequan" => "Ha Nội",
							"tuoi" => 22			
				);
				print_r($n);
				print_r($arr1);
				print_r($arr2);
				print_r($arr3);
				print_r($arr4);
			?>
		</div>

	</body>
</html>