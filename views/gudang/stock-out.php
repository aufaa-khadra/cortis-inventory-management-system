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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quantity = (int) $_POST['quantity'];
    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

    $user_id = $_SESSION['id_user'];

    if ($quantity > $product['stock']) {

        $error = "Insufficient stock!";

    } else {

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
                'Out',
                $quantity,
                '$description'
            )
        ");

        mysqli_query($conn, "
            UPDATE products
            SET stock = stock - $quantity
            WHERE id = $id
        ");

        header("Location: products.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Out</title>
</head>
<body>

<h1>Stock Out</h1>

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

<?php if (!empty($error)) : ?>
    <p style="color:red;">
        <?= $error ?>
    </p>
<?php endif; ?>

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

    <label>Reason</label>
<br>

    <select name="description" required>
    <option value="">Select Reason</option>
    <option value="Sold to customer">Sold to customer</option>
    <option value="Damaged item">Damaged item</option>
    <option value="Lost item">Lost item</option>
    <option value="Event merchandise">Event merchandise</option>
    </select>
    <br><br>

    <button type="submit">
        Save Stock Out
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