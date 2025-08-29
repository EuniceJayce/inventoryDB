<?php
include 'db.php';

if (isset($_POST['user_id'])) {
    $id = $_POST['user_id'];

    // First, set all references in purchase_orders to NULL
    $sql1 = "UPDATE purchase_orders SET user_id = NULL WHERE user_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    // Now delete the user
    $sql2 = "DELETE FROM users WHERE user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id);

    if ($stmt2->execute()) {
        // Restart IDs safely
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");

        // Reorder IDs in users table
        $conn->query("SET @count = 0");
        $conn->query("UPDATE users SET user_id = @count:=@count+1 ORDER BY user_id");

        // Reset auto increment
        $conn->query("ALTER TABLE users AUTO_INCREMENT = 1");

        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        header("Location: users.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
