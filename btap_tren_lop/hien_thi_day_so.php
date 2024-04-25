<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .even {
            color: red;
            font-weight: bold;
        }
        .odd {
            color: blue;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php
        for($i=1; $i<=200;$i++)
        {
            if($i % 2 == 0) {
                echo "<span class='even'>$i</span> ";
            } else {
                echo "<span class='odd'>$i</span> ";
            }

        }
        
    ?>
</body>
</html>