<?php
include 'db.php';

if (isset($_POST['category_id'])) {
    $id = $_POST['category_id'];

    // Delete category
    $sql = "DELETE FROM categories WHERE category_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ⚠️ Resetting IDs will break foreign keys in products
        // Only safe if products.category_id has no rows or foreign key removed

        $conn->query("SET @count = 0");
        $conn->query("UPDATE categories SET category_id = @count:=@count+1 ORDER BY category_id");
        $conn->query("ALTER TABLE categories AUTO_INCREMENT = 1");

        header("Location: categories.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
