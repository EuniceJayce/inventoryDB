<?php
include 'db.php';
session_start();

// ✅ Handle Add Order form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_order'])) {
    $supplier_id = intval($_POST['supplier_id']);
    $user_id     = intval($_POST['user_id']);
    $order_date  = $_POST['order_date'];
    $status = ucfirst(strtolower(trim($_POST['status'])));


    // Debugging: check what was sent
    // echo "STATUS: " . $status; exit;

    $stmt = $conn->prepare("INSERT INTO purchase_orders (supplier_id, user_id, order_date, status) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $supplier_id, $user_id, $order_date, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ New purchase order created.";
    } else {
        $_SESSION['error'] = "❌ Error: " . $conn->error;
    }

    header("Location: purchase_orders.php");
    exit();
}


?>
