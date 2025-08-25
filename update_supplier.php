<?php
include 'db.php';

if (isset($_POST['supplier_id'])) {
    $id = $_POST['supplier_id'];
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "UPDATE suppliers 
            SET name=?, contact_person=?, phone=?, email=?, address=? 
            WHERE supplier_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $name, $contact_person, $phone, $email, $address, $id);

    if ($stmt->execute()) {
        // Reset IDs (optional like products)
        $conn->query("SET @count = 0");
        $conn->query("UPDATE suppliers SET supplier_id = @count:=@count+1 ORDER BY supplier_id");
        $conn->query("ALTER TABLE suppliers AUTO_INCREMENT = 1");

        header("Location: suppliers.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
