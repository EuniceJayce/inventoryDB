<?php
include 'db.php';

if (isset($_POST['update'])) {
    $order_id    = $_POST['order_id'];
    $supplier_id = $_POST['supplier_id'];
    $order_date  = $_POST['order_date'];
    $status      = $_POST['status'];

    $sql = "UPDATE purchase_orders SET supplier_id=?, order_date=?, status=? WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $supplier_id, $order_date, $status, $order_id);

    if ($stmt->execute()) {
        header("Location: purchase_orders.php?updated=1");
        exit;
    } else {
        echo "Error updating order: " . $conn->error;
    }
}
?>
