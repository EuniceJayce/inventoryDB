<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Find user
$sql = "SELECT * FROM users WHERE username = ? AND status = 'Active' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ⚠️ For now plaintext password (later we can hash with password_hash)
    if($user['password'] === $password) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php"); // go to dashboard
        exit;
    } else {
        $_SESSION['error'] = "Invalid password.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "User not found or inactive.";
    header("Location: login.php");
    exit;
}
