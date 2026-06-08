<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SESSION['role'] == 'Gudang') {
    header("Location: views/gudang/dashboard.php");
} elseif ($_SESSION['role'] == 'Manager') {
    header("Location: views/manager/dashboard.php");
}
exit();
?>