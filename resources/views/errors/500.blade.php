<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Server Error - 500</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            text-align: center;
            padding: 100px;
        }
        h1 {
            font-size: 50px;
            color: #e74c3c;
        }
        p {
            font-size: 20px;
            color: #333;
        }
        a {
            text-decoration: none;
            background: #3490dc;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <h1>500</h1>
    <p>Sorry! Something went wrong on our end.</p>
    <a href="{{ url('/') }}">Go to Home</a>

</body>
</html>
