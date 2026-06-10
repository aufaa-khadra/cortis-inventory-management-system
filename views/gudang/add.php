<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

if (isset($_POST['save'])) {

    $category_id = $_POST['category_id'];
    $item_code = mysqli_real_escape_string($conn, $_POST['item_code']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = $_POST['price'];

    $query = "INSERT INTO products (
                category_id,
                item_code,
                item_name,
                price,
                stock
            ) VALUES (
                '$category_id',
                '$item_code',
                '$item_name',
                '$price',
                0
            )";

    if (mysqli_query($conn, $query)) {
        header("Location: products.php");
        exit();
    } else {
        echo "Failed to save product.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>

<h1>Add Product</h1>

<form method="POST">

    <p>Category</p>
    <select name="category_id" required>
        <option value="">Select Category</option>

        <?php while($category = mysqli_fetch_assoc($categories)) : ?>
            <option value="<?= $category['id'] ?>">
                <?= htmlspecialchars($category['category_name']) ?>
            </option>
        <?php endwhile; ?>

    </select>

    <br><br>

    <p>Item Code</p>
    <input
        type="text"
        name="item_code"
        required
    >

    <br><br>

    <p>Product Name</p>
    <input
        type="text"
        name="item_name"
        required
    >

    <br><br>

    <p>Price</p>
    <input
        type="number"
        name="price"
        required
    >

    <br><br>

    <button type="submit" name="save">
        Save Product
    </button>

</form>

<br>

<a href="products.php">
    <button>Back</button>
</a>

</body>
</html>