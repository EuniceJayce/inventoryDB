<?php include 'db.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $user_id = $_POST['user_id'];
    $order_date = $_POST['order_date'];
    $status = $_POST['status'];

    $sql = "INSERT INTO purchase_orders (supplier_id, user_id, order_date, status) 
            VALUES ('$supplier_id', '$user_id', '$order_date', '$status')";

    if ($conn->query($sql) === TRUE) {
        header("Location: purchase_orders.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Order - Inventory Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow">
    <h3 class="mb-4">Create New Purchase Order</h3>
    <form method="POST">
      <!-- Supplier Dropdown -->
      <div class="mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select" required>
          <option value="">Select Supplier</option>
          <?php
          $suppliers = $conn->query("SELECT supplier_id, name FROM suppliers");
          while ($row = $suppliers->fetch_assoc()) {
              echo "<option value='{$row['supplier_id']}'>{$row['name']}</option>";
          }
          ?>
        </select>
      </div>

      <!-- Ordered By (User Dropdown) -->
      <div class="mb-3">
        <label class="form-label">Ordered By</label>
        <select name="user_id" class="form-select" required>
          <option value="">Select User</option>
          <?php
          $users = $conn->query("SELECT user_id, username FROM users");
          while ($row = $users->fetch_assoc()) {
              echo "<option value='{$row['user_id']}'>{$row['username']}</option>";
          }
          ?>
        </select>
      </div>

      <!-- Date -->
      <div class="mb-3">
        <label class="form-label">Order Date</label>
        <input type="date" name="order_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
      </div>

      <!-- Status -->
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="Pending" selected>Pending</option>
          <option value="Approved">Approved</option>
          <option value="Rejected">Rejected</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Save Order</button>
      <a href="purchase_orders.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>
