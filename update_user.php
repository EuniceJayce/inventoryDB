<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id       = $_POST['user_id'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];
    $status   = $_POST['status'];

    $sql = "UPDATE users SET username=?, email=?, role=?, status=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $role, $status, $id);

    if ($stmt->execute()) {
        header("Location: users.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
