<!DOCTYPE html>
<html>
<head>
    <title>Page Not Found</title>
    <style>
        /* public/css/404.css */

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        .container h1 {
            font-size: 6em;
            margin: 0;
            color: #333;
        }

        .container p {
            font-size: 1.5em;
            margin: 10px 0;
            color: #666;
        }

        .container a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .container a:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container h1 {
                font-size: 4em;
            }
            .container p {
                font-size: 1.2em;
            }
        }

    </style>
</head>
<body>
<div class="container">
    <h1>Oops! Page not found.</h1>
    <p>The page you're looking for doesn't exist. <a href="/admin">Go back</a></p>
</div>
</body>
</html>
