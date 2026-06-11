<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Gudang') {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../config/connection.php';

$query = mysqli_query($conn, "
    SELECT
        st.*,
        p.item_name,
        u.full_name
    FROM stock_transactions st
    JOIN products p
        ON st.product_id = p.id
    JOIN users u
        ON st.user_id = u.id
    ORDER BY st.transaction_date DESC
");
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