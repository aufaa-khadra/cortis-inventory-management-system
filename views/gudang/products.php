<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$query = mysqli_query($conn, "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

<h1>Products</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>Code</th>
        <th>Image</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($query)) : ?>
    <tr>
        <td><?= $row['item_code'] ?></td>

        <td>
            <img
                src="../../assets/images/<?= $row['image'] ?>"
                width="80"
            >
        </td>

        <td><?= htmlspecialchars($row['item_name']) ?></td>

        <td><?= htmlspecialchars($row['category_name']) ?></td>

        <td>Rp <?= number_format($row['price']) ?></td>

        <td><?= $row['stock'] ?></td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>