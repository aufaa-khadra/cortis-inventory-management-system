<?php
session_start();

require_once '../config/connection.php';

if (isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

$error_message = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query  = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password']) || $password === $row['password']) {
            
            $_SESSION['id_user']   = $row['id'];
            $_SESSION['username']  = $row['username'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role']      = $row['role'];

            header("Location: ../index.php");
            exit();

        } else {
            $error_message = "Username atau Password salah!";
        }
    } else {
        $error_message = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CORTIS Merch</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 5px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .login-container p {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #111;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background-color: #333;
        }
        .alert-error {
            background-color: #ffebe9;
            color: #ff3333;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 15px;
            border: 1px solid #ffcccc;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>CORTIS SYSTEM</h2>
        <p>Sign in to manage merchandise</p>

        <?php if (!empty($error_message)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login" class="btn-login">
                Sign In
            </button>
        </form>
    </div>

</body>
</html>