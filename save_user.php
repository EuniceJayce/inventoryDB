<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hashed password
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "INSERT INTO users (username, email, password, role, status)
            VALUES ('$username', '$email', '$password', '$role', '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: users.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
