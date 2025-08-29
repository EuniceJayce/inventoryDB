<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM purchase_orders WHERE order_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ✅ Reset order_id sequence after delete
        $conn->query("SET @count = 0");
        $conn->query("UPDATE purchase_orders SET order_id = @count:=@count +1 ORDER BY order_id");
        $conn->query("ALTER TABLE purchase_orders AUTO_INCREMENT = 1");

        header("Location: purchase_orders.php");
        exit;
    } else {
        echo "<script>
                alert('❌ Cannot delete this order. It may be linked to other records.');
                window.location='purchase_orders.php';
              </script>";
    }
}
?>
