<?php
include 'db.php';

if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];

    $sql = "UPDATE products 
            SET name=?, stock=?, price=?, category_id=?, supplier_id=? 
            WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidiii", $name, $stock, $price, $category_id, $supplier_id, $id);

    if ($stmt->execute()) {
        header("Location: products.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
