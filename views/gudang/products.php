<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM products
        WHERE id = $id
    ");

    header("Location: products.php");
    exit();
}

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

<a href="add.php">
    <button>Add Product</button>
</a>

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
        <th>Action</th>
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

        <td>

            <a href="edit.php?id=<?= $row['id'] ?>">
                <button>Edit</button>
            </a>

            <a href="products.php?delete=<?= $row['id'] ?>"
               onclick="return confirm('Delete this product?')">
                <button>Delete</button>
            </a>

        </td>

    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>