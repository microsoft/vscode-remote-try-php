<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <script>
        function goToIndex() {
                window.location.href = 'index.php';
        }
        window.onload = function() {
            document.getElementById("name").focus();
        };
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 40%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: navy;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        input {
            width: 300px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 150px;
            background-color: #0056b3;
            color: #fff;
            border: none #004288;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #004288;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="process_add" method="POST">
            <!-- <input type="text" placeholder="ID Customer" id="idCustomer" name="idCustomer" required> -->
            <input type="text" placeholder="Name" id="name" name="name" required>
            <input type="text" placeholder="Phone" id="phone" name="phone" required>
            <input type="email" placeholder="Email" id="email" name="email" required>
            <input type="password" placeholder="Password" id="password" name="password" required>
            <input type="submit" value="Rgister">
            <input type="submit" value="Cancel" onclick='goToIndex()'>
        </form>
    </div>
</body>
</html>
