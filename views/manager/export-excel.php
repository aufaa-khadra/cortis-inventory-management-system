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

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=transaction_report.xls");
?>

<table border="1">

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
            <?= $row['transaction_date'] ?>
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