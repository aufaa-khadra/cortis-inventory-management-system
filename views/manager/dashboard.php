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

<h1>
    Welcome,
    <?= htmlspecialchars($_SESSION['full_name']) ?>
</h1>

<br>

<a href="products.php">
    <button>
        Products
    </button>
</a>

<a href="transactions.php">
    <button>
        Transactions
    </button>
</a>

<a href="../../auth/logout.php">
    <button>
        Logout
    </button>
</a>

</body>
</html>