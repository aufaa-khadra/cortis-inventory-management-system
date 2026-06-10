<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = (int) $_GET['id'];

$product = mysqli_query($conn, "
    SELECT * FROM products
    WHERE id = $id
");

$product = mysqli_fetch_assoc($product);

if (!$product) {
    header("Location: products.php");
    exit();
}

$categories = mysqli_query($conn, "
    SELECT * FROM categories
    ORDER BY category_name ASC
");

if (isset($_POST['update'])) {

    $category_id = $_POST['category_id'];
    $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = $_POST['price'];

    $query = "
        UPDATE products
        SET
            category_id = '$category_id',
            item_code = '$item_code',
            item_name = '$item_name',
            price = '$price'
        WHERE id = $id
    ";

    if (mysqli_query($conn, $query)) {
        header("Location: products.php");
        exit();
    } else {
        echo "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
</head>
<body>

<h1>Edit Product</h1>

<form method="POST">

    <p>Category</p>
    <select name="category_id" required>

        <?php while($category = mysqli_fetch_assoc($categories)) : ?>
            <option
                value="<?= $category['id'] ?>"
                <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($category['category_name']) ?>
            </option>
        <?php endwhile; ?>

    </select>

    <br><br>

    <p>Item Code</p>
    <input
        type="text"
        name="item_code"
        value="<?= htmlspecialchars($product['item_code']) ?>"
        required
    >

    <br><br>

    <p>Product Name</p>
    <input
        type="text"
        name="item_name"
        value="<?= htmlspecialchars($product['item_name']) ?>"
        required
    >

    <br><br>

    <p>Price</p>
    <input
        type="number"
        name="price"
        value="<?= $product['price'] ?>"
        required
    >

    <br><br>

    <button type="submit" name="update">
        Update Product
    </button>

</form>

<br>

<a href="products.php">
    <button>Back</button>
</a>

</body>
</html>