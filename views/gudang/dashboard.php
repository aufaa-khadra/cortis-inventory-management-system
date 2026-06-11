<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Gudang</title>
</head>
<body>

    <h1>Login Berhasil! Selamat Datang <?= htmlspecialchars($_SESSION['full_name']) ?></h1>

    <a href="../../auth/logout.php">
        <button>Logout</button>
    </a>

    <a href="products.php">
    <button>Products</button>
</a>

<a href="transactions.php">
    <button>Transactions</button>
</a>

</body>
</html>