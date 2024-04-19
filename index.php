
<?php

?>
<html>
	<head>
		<title>Visual Studio Code Remote :: PHP</title>
	</head>
	<body>
	<?php 
		$so_luong = $_POST["so_luong"];
        $don_gia = $_POST["don_gia"];
        if ($so_luong < 10)
        {
            $thanh_tien = $don_gia * $so_luong;
        } elseif ($so_luong >= 10 and $so_luong <= 20)
        {
            $thanh_tien = $don_gia * $so_luong * 0.95;
        } else {
            $thanh_tien = $don_gia * $so_luong * 0.9;
        }
		// phpinfo(); 
	?>
	</body>
</html>