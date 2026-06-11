<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Manager') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
    SELECT
        p.item_code,
        p.item_name,
        c.category_name,
        p.price,
        p.stock
    FROM products p
    JOIN categories c
        ON p.category_id = c.id
    WHERE 1
";

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $sql .= " AND p.item_name LIKE '%$search_safe%'";
}

if (!empty($category)) {
    $sql .= " AND p.category_id = " . (int)$category;
}

$sql .= " ORDER BY p.item_name ASC";

$query = mysqli_query($conn, $sql);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=products_report.xls");
?>

<table border="1">

    <tr>
        <th>Code</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($query)) : ?>

    <tr>

        <td>
            <?= htmlspecialchars($row['item_code']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['item_name']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['category_name']) ?>
        </td>

        <td>
            Rp <?= number_format($row['price']) ?>
        </td>

        <td>
            <?= $row['stock'] ?>
        </td>

    </tr>

    <?php endwhile; ?>

</table>