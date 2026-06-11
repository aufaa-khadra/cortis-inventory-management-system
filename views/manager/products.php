<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Manager') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$categories = mysqli_query($conn, "
    SELECT *
    FROM categories
    ORDER BY category_name ASC
");

$sql = "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE 1
";

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $sql .= " AND p.item_name LIKE '%$search_safe%'";
}

if (!empty($category)) {
    $sql .= " AND p.category_id = " . (int)$category;
}

$sql .= " ORDER BY p.id DESC";

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

<h1>Products</h1>

<a href="dashboard.php">
    <button>Back</button>
</a>

<br><br>

<form method="GET">

    <input
        type="text"
        name="search"
        placeholder="Search product..."
        value="<?= htmlspecialchars($search) ?>"
    >

    <select name="category">

        <option value="">
            All Categories
        </option>

        <?php while($cat = mysqli_fetch_assoc($categories)) : ?>

            <option
                value="<?= $cat['id'] ?>"
                <?= ($category == $cat['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($cat['category_name']) ?>
            </option>

        <?php endwhile; ?>

    </select>

    <button type="submit">
        Filter
    </button>

    <a href="products.php">
        <button type="button">
            Reset
        </button>
    </a>

    <a href="export-products.php?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>">
    <button type="button">
        Export Products Excel
    </button>
</a>

</form>

<br>

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

        <td><?= htmlspecialchars($row['item_code']) ?></td>

        <td>
            <img
                src="../../assets/images/<?= htmlspecialchars($row['image']) ?>"
                width="80"
                alt="Product Image"
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