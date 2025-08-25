<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Orders - Inventory Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .sidebar { height: 100vh; background: #0d6efd; color: white; }
    .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin: 4px 0; }
    .sidebar a:hover { background: #0b5ed7; }
    .content { padding: 20px; }
    .card { border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-3">
        <h4 class="mb-4"><i class="fa fa-box"></i> Inventory</h4>
        <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="products.php"><i class="fa fa-cubes"></i> Products</a>
        <a href="categories.php"><i class="fa fa-tags"></i> Categories</a>
        <a href="suppliers.php"><i class="fa fa-truck"></i> Suppliers</a>
        <a href="purchase_orders.php"><i class="fa fa-file-invoice"></i> Purchase Orders</a>
        <a href="reports.php"><i class="fa fa-file"></i> Reports</a>
        <a href="users.php"><i class="fa fa-users"></i> Users</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Purchase Orders</h2>
          <a href="add_order.php" class="btn btn-primary"><i class="fa fa-plus"></i> New Order</a>
        </div>

        <div class="card">
          
          <div class="card-body">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Order ID</th>
                  <th>Supplier</th>
                  <th>Ordered By</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php

                $sql = "SELECT o.order_id, s.name AS supplier, u.username AS ordered_by, 
               o.order_date, o.status
                FROM purchase_orders o
                JOIN suppliers s ON o.supplier_id = s.supplier_id
                JOIN users u ON o.user_id = u.user_id
                ORDER BY o.order_date DESC";

                $result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
      $status = ucfirst(strtolower($row['status']));

      if ($status == 'Pending') {
          $badgeClass = 'warning';
      } elseif ($status == 'Approved') {
          $badgeClass = 'success';
      } elseif ($status == 'Reject' || $status == 'Rejected') {
          $badgeClass = 'danger';
      } else {
          $badgeClass = 'secondary';
      }
?>
  <tr>
    <td><?= $row['order_id'] ?></td>
    <td><?= $row['supplier'] ?></td>
    <td><?= $row['ordered_by'] ?></td>
    <td><?= $row['order_date'] ?></td>
    <td><span class="badge bg-<?= $badgeClass ?>"><?= $status ?></span></td>
    <td>
      <!-- Edit Button -->
      <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['order_id'] ?>">
        <i class="fa fa-edit"></i>
      </button>

      <!-- Delete Button -->
      <a href="delete_order.php?id=<?= $row['order_id'] ?>" 
         class="btn btn-sm btn-danger" 
         onclick="return confirm('Are you sure you want to delete this order?');">
         <i class="fa fa-trash"></i>
      </a>
    </td>
  </tr>

  <!-- Modal -->
  <div class="modal fade" id="editModal<?= $row['order_id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Order #<?= $row['order_id'] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="update_order.php">
          <div class="modal-body">
            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">

            <!-- Supplier Dropdown -->
            <div class="mb-3">
              <label class="form-label">Supplier</label>
              <select name="supplier_id" class="form-control" required>
                <?php
                $suppliers2 = $conn->query("SELECT * FROM suppliers");
                while ($sup2 = $suppliers2->fetch_assoc()) {
                  $selected = ($sup2['name'] == $row['supplier']) ? "selected" : "";
                  echo "<option value='{$sup2['supplier_id']}' $selected>{$sup2['name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Date -->
            <div class="mb-3">
              <label class="form-label">Order Date</label>
              <input type="date" name="order_date" class="form-control" value="<?= $row['order_date'] ?>" required>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-control">
                <option value="Pending"  <?= ($row['status']=="Pending" ? "selected" : "") ?>>Pending</option>
                <option value="Approved" <?= ($row['status']=="Approved" ? "selected" : "") ?>>Approved</option>
                <option value="Rejected" <?= ($row['status']=="Rejected" ? "selected" : "") ?>>Rejected</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php
  }
} else {
  echo "<tr><td colspan='6' class='text-center'>No orders found</td></tr>";
}
?>

              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
