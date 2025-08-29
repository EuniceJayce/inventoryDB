<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("UPDATE purchase_orders SET status='delivered' WHERE order_id=?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ Order marked as delivered.";
    } else {
        $_SESSION['error'] = "❌ Error: " . $conn->error;
    }
}

header("Location: purchase_orders.php");
exit();
