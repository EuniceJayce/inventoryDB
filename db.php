<?php
$servername = "localhost";  // or "127.0.0.1"
$username = "root";
$password = ""; // default in XAMPP is empty
$dbname = "inventory_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
