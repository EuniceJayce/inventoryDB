<?php
include 'db.php';

if (isset($_POST['supplier_id'])) {
    $id = $_POST['supplier_id'];

    $sql = "DELETE FROM suppliers WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Reset IDs (optional)
        $conn->query("SET @count = 0");
        $conn->query("UPDATE suppliers SET supplier_id = @count:=@count+1 ORDER BY supplier_id");
        $conn->query("ALTER TABLE suppliers AUTO_INCREMENT = 1");

        header("Location: suppliers.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
