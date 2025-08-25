<?php
include 'db.php';

if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];

    // Delete product
    $sql = "DELETE FROM products WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Reset IDs
        $conn->query("SET @count = 0");
        $conn->query("UPDATE products SET product_id = @count:=@count+1 ORDER BY product_id");
        $conn->query("ALTER TABLE products AUTO_INCREMENT = 1");

        header("Location: products.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
