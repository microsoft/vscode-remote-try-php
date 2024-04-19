<!DOCTYPE html>
<html>
<head>
    <title>Visual Studio Code Remote :: PHP</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<?php 
for ($i = 1; $i <= 200; $i++) {
    if ($i % 2 == 0) {
        echo "<span class='even'>$i</span><br>"; // Sử dụng thẻ span với class 'even' để áp dụng CSS
    } else {
        echo "<span class='evenn'>$i</span><br>";
    }
}
?>
</body>
</html>
