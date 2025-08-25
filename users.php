<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: inventory/login.php");
    exit;
}
?>

<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users - Inventory Management</title>
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
      <a href="users.php" style="background:#0b5ed7;"><i class="fa fa-users"></i> Users</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 content">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Users</h2>
        <!-- Button trigger modal -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
          <i class="fa fa-user-plus"></i> New User
        </button>
      </div>

      <div class="card">
        <div class="card-header"><h5>User Accounts</h5></div>
        <div class="card-body">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM users";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['user_id']}</td>
                          <td>{$row['username']}</td>
                          <td>{$row['email']}</td>
                          <td><span class='badge bg-".($row['role']=='Admin'?'danger':($row['role']=='Staff'?'primary':'secondary'))."'>{$row['role']}</span></td>
                          <td><span class='badge bg-".($row['status']=='Active'?'success':'danger')."'>{$row['status']}</span></td>
                          <td>
                            <button class='btn btn-sm btn-warning'><i class='fa fa-edit'></i></button>
                            <button class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></button>
                          </td>
                        </tr>";
                }
              } else {
                echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="save_user.php">
        <div class="modal-header">
          <h5 class="modal-title">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="Admin">Admin</option>
              <option value="Staff">Staff</option>
              <option value="Viewer">Viewer</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
