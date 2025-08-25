<?php
$host = "localhost";  // XAMPP default
$user = "root";       // XAMPP default user
$pass = "";           // XAMPP default password is empty
$db   = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
