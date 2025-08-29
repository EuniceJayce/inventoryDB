<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// ✅ Handle Add Order form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_order'])) {
    $supplier_id = $_POST['supplier_id'];
    $user_id     = $_POST['user_id'];
    $order_date  = $_POST['order_date'];

    // 👇 Always start as pending
    $status = "pending";

    $stmt = $conn->prepare("INSERT INTO purchase_orders (supplier_id, user_id, order_date, status) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $supplier_id, $user_id, $order_date, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "✅ New purchase order created.";
    } else {
        $_SESSION['error'] = "❌ Error: " . $conn->error;
    }

    header("Location: purchase_orders.php");
    exit();


}
?>

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
        <a href="purchase_orders.php" style="background:#0b5ed7;"><i class="fa fa-file-invoice"></i> Purchase Orders</a>
        <a href="reports.php"><i class="fa fa-file"></i> Reports</a>
        <a href="users.php"><i class="fa fa-users"></i> Users</a>
        <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Purchase Orders</h2>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <i class="fa fa-plus"></i> New Order
          </button>
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
                  <th> </th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT o.order_id, s.supplier_id, s.name AS supplier, u.username AS ordered_by, 
                        o.order_date, o.status
                        FROM purchase_orders o
                        LEFT JOIN suppliers s ON o.supplier_id = s.supplier_id
                        LEFT JOIN users u ON o.user_id = u.user_id
                        ORDER BY o.order_id ASC";

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                    
                    // ✅ Status badges like users.php
                    $statusValue = strtolower(trim($row['status']));
                    switch($statusValue) {
                        case 'pending':  
                            $badgeClass = 'warning'; 
                            $statusText = 'Pending';
                            break;
                        case 'approved': 
                            $badgeClass = 'success'; 
                            $statusText = 'Approved';
                            break;
                        case 'rejected': 
                            $badgeClass = 'danger';  
                            $statusText = 'Rejected';
                            break;
                        default:         
                            $badgeClass = 'secondary'; 
                            $statusText = ucfirst($statusValue);
                            break;
                    }
                ?>
                  <tr>
                    <td><?= htmlspecialchars($row['order_id']) ?></td>
                    <td><?= htmlspecialchars($row['supplier']) ?></td>
                    <td><?= htmlspecialchars($row['ordered_by']) ?></td>
                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                    <td>
                      <?php if ($statusValue == 'pending'): ?>
                        <form method="POST" action="mark_delivered.php" style="display:inline;">
                          <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                          <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check"></i> Mark as Delivered
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="badge bg-dark">Delivered</span>
                      <?php endif; ?>
                    </td>

                    <td>
                      <a href="delete_order.php?id=<?= $row['order_id'] ?>" 
                        class="btn btn-sm btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this order?');">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>


                  </tr>

                  <!-- Edit Modal -->
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
                                  $selected = ($sup2['supplier_id'] == $row['supplier_id']) ? "selected" : "";
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
                              <select name="status" class="form-select" required>
                                <option value="Pending"  <?= (strtolower(trim($row['status'])) == 'pending')  ? 'selected' : '' ?>>Pending</option>
                                <option value="Approved" <?= (strtolower(trim($row['status'])) == 'approved') ? 'selected' : '' ?>>Approved</option>
                                <option value="Rejected" <?= (strtolower(trim($row['status'])) == 'rejected') ? 'selected' : '' ?>>Rejected</option>
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

  <!-- Add Order Modal -->
  <div class="modal fade" id="addOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create New Purchase Order</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <!-- Supplier Dropdown -->
            <div class="mb-3">
              <label class="form-label">Supplier</label>
              <select name="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                <?php
                $suppliers = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name ASC");
                while ($row = $suppliers->fetch_assoc()) {
                    echo "<option value='{$row['supplier_id']}'>{$row['name']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Ordered By -->
            <div class="mb-3">
              <label class="form-label">Ordered By</label>
              <select name="user_id" class="form-select" required>
                <option value="">Select User</option>
                <?php
                $users = $conn->query("SELECT user_id, username FROM users ORDER BY username ASC");
                while ($row = $users->fetch_assoc()) {
                    echo "<option value='{$row['user_id']}'>{$row['username']}</option>";
                }
                ?>
              </select>
            </div>

            <!-- Order Date -->
            <div class="mb-3">
              <label class="form-label">Order Date</label>
              <input type="date" name="order_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_order" class="btn btn-primary">Save Order</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
