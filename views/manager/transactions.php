<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Manager') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$product = $_GET['product'] ?? '';
$type = $_GET['type'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$products = mysqli_query($conn, "
    SELECT id, item_name
    FROM products
    ORDER BY item_name ASC
");

$sql = "
    SELECT
        st.*,
        p.item_name,
        u.full_name
    FROM stock_transactions st
    JOIN products p
        ON st.product_id = p.id
    JOIN users u
        ON st.user_id = u.id
    WHERE 1
";

if (!empty($product)) {
    $sql .= " AND st.product_id = " . (int)$product;
}

if (!empty($type)) {
    $type_safe = mysqli_real_escape_string($conn, $type);
    $sql .= " AND st.transaction_type = '$type_safe'";
}

if (!empty($start_date)) {
    $sql .= " AND DATE(st.transaction_date) >= '$start_date'";
}

if (!empty($end_date)) {
    $sql .= " AND DATE(st.transaction_date) <= '$end_date'";
}

$sql .= " ORDER BY st.transaction_date DESC";

$query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
</head>
<body>

<h1>Transaction History</h1>

<a href="dashboard.php">
    <button>Back</button>
</a>

<br><br>

<form method="GET">

    <select name="product">

        <option value="">
            All Products
        </option>

        <?php while($p = mysqli_fetch_assoc($products)) : ?>

            <option
                value="<?= $p['id'] ?>"
                <?= ($product == $p['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($p['item_name']) ?>
            </option>

        <?php endwhile; ?>

    </select>

    <select name="type">

        <option value="">
            All Types
        </option>

        <option
            value="In"
            <?= ($type == 'In') ? 'selected' : '' ?>
        >
            Stock In
        </option>

        <option
            value="Out"
            <?= ($type == 'Out') ? 'selected' : '' ?>
        >
            Stock Out
        </option>

    </select>

    <input
        type="date"
        name="start_date"
        value="<?= htmlspecialchars($start_date) ?>"
    >

    <input
        type="date"
        name="end_date"
        value="<?= htmlspecialchars($end_date) ?>"
    >

    <button type="submit">
        Filter
    </button>

    <a href="transactions.php">
        <button type="button">
            Reset
        </button>
    </a>

    <a href="export-excel.php?product=<?= urlencode($product) ?>&type=<?= urlencode($type) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
    <button type="button">
        Export Excel
    </button>
</a>

</form>

<br>

<table border="1" cellpadding="10">

    <tr>
        <th>Date</th>
        <th>Product</th>
        <th>Type</th>
        <th>Quantity</th>
        <th>User</th>
        <th>Description</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($query)) : ?>

    <tr>

        <td>
            <?= date('d M Y H:i', strtotime($row['transaction_date'])) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['item_name']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['transaction_type']) ?>
        </td>

        <td>
            <?= $row['quantity'] ?>
        </td>

        <td>
            <?= htmlspecialchars($row['full_name']) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['description']) ?>
        </td>

    </tr>

    <?php endwhile; ?>

</table>

</body>
</html>