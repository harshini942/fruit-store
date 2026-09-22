<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container { width: 100%; max-width: 400px; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 40px; text-align: center; }
        .icon { font-size: 60px; margin-bottom: 20px; }
        .card h2 { color: #333; margin-bottom: 10px; }
        .card p { color: #666; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #5a67d8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">✅</div>
            <h2>Password Reset Successfully!</h2>
            <p>You can now login with your new password.</p>
            <a href="login.php" class="btn">Login Now</a>
        </div>
    </div>
</body>
</html>