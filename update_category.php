<?php
include 'db.php';

if (isset($_POST['category_id'])) {
    $id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $sql = "UPDATE categories SET name=?, description=? WHERE category_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $description, $id);

    if ($stmt->execute()) {
        header("Location: categories.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
