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
    SELECT *
    FROM products
    WHERE id = $id
");

$product = mysqli_fetch_assoc($product);

if (!$product) {
    die("Product not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = (int) $_POST['quantity'];
    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

   $user_id = $_SESSION['id_user'];

    mysqli_query($conn, "
        INSERT INTO stock_transactions
        (
            product_id,
            user_id,
            transaction_type,
            quantity,
            description
        )
        VALUES
        (
            $id,
            $user_id,
            'In',
            $quantity,
            '$description'
        )
    ");

    mysqli_query($conn, "
        UPDATE products
        SET stock = stock + $quantity
        WHERE id = $id
    ");

    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock In</title>
</head>
<body>

<h1>Stock In</h1>

<p>
    Product:
    <strong>
        <?= htmlspecialchars($product['item_name']) ?>
    </strong>
</p>

<p>
    Current Stock:
    <strong>
        <?= $product['stock'] ?>
    </strong>
</p>

<form method="POST">

    <label>Quantity</label>
    <br>
    <input
        type="number"
        name="quantity"
        min="1"
        required
    >

    <br><br>

    <select name="description" required>
    <option value="">Select Reason</option>
    <option value="Restock from supplier">Restock from supplier</option>
    <option value="New production batch">New production batch</option>
    <option value="Returned item">Returned item</option>
    <option value="Stock adjustment">Stock adjustment</option>
</select>

    <br><br>

    <button type="submit">
        Save Stock In
    </button>

</form>

<br>

<a href="products.php">
    <button>
        Back
    </button>
</a>

</body>
</html>