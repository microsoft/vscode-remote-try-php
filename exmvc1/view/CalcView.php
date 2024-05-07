<!DOCTYPE html>
<html>
<head>
    <title>Calculation using MVC Model</title>
    <style type="text/css">
        .box {
            width: 300px;
            padding: 20px;
            border: 4px solid #279;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Calculation using MVC Model</h2>
        <p>Result: <?php echo isset($calc->result) ? $calc->result : ''; ?></p>
    </div>
</body>
</html>
