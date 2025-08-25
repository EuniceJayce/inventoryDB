<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    // Prepared statement
    $stmt = $conn->prepare("INSERT INTO products (name, category_id, supplier_id, stock, price) 
                            VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Debug if query wrong
    }

    $stmt->bind_param("siiid", $name, $category_id, $supplier_id, $stock, $price);

    if ($stmt->execute()) {
        // ✅ Redirect to products.php page after saving
        header("Location: products.php");
        exit();
    } else {
        die("Execute failed: " . $stmt->error); // Debug if insert failed
    }

    $stmt->close();
}
?>
