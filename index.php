<?php

/*----------------------------------------------------------------------------------------
 * Copyright (c) Microsoft Corporation. All rights reserved.
 * Licensed under the MIT License. See LICENSE in the project root for license information.
 *---------------------------------------------------------------------------------------*/

function sayHello($name) {
	echo "Hello $name!";
}


	$stukhoa = $_REQUEST["txtTuKhoa"];

?>

<script>
	function showMessage() {
		alert("You clicked the button!");
	};
</script>
<html>
	<head>
		<title>PHP course</title>
	</head>
	<body>
		<button onclick="showMessage()">Click Me</button>
		<?php 
		
		sayHello('= xin chao ');
				
		?>
	</body>
</html>