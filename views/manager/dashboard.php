<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Manager') {
    header("Location: ../../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Manager</title>
</head>
<body>

    <h1>Login Berhasil! Selamat Datang <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

    <a href="../../auth/logout.php">
        <button>Logout</button>
    </a>

</body>
</html>