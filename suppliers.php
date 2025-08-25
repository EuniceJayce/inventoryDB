<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'db.php'; ?>

<?php
if (isset($_POST['add_supplier'])) {
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $contact_person, $phone, $email, $address);
    $stmt->execute();

    header("Location: suppliers.php"); // refresh
    exit();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers - Inventory Management</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background: #0d6efd;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px;
      border-radius: 8px;
      margin: 4px 0;
    }
    .sidebar a:hover {
      background: #0b5ed7;
    }
    .content {
      padding: 20px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
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
          <h2>Suppliers</h2>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="fa fa-plus"></i> Add Supplier
          </button>
        </div>

        <!-- Suppliers Table -->
        <div class="card">
          <div class="card-body">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Contact Person</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Address</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = $conn->query("SELECT * FROM suppliers");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['supplier_id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['contact_person']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['address']}</td>
                            <td>
                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editSupplierModal{$row['supplier_id']}'><i class='fa fa-edit'></i></button>
                                <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteSupplierModal{$row['supplier_id']}'><i class='fa fa-trash'></i></button>

                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No suppliers found</td></tr>";
                }
                ?>
                </tbody>

            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <?php
$result->data_seek(0); // reset pointer
while ($row = $result->fetch_assoc()) {
?>
<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal<?php echo $row['supplier_id']; ?>" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="update_supplier.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title">Edit Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id']; ?>">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Supplier Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Contact Person</label>
              <input type="text" name="contact_person" class="form-control" value="<?php echo $row['contact_person']; ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?php echo $row['phone']; ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="3"><?php echo $row['address']; ?></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update Supplier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Supplier Modal -->
<div class="modal fade" id="deleteSupplierModal<?php echo $row['supplier_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="delete_supplier.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="supplier_id" value="<?php echo $row['supplier_id']; ?>">
          Are you sure you want to delete <strong><?php echo $row['name']; ?></strong>?
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>


  <!-- Add Supplier Modal -->
  <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
        <form method="POST" action="suppliers.php">
            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label">Supplier Name</label>
                <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label">Contact Person</label>
                <input type="text" name="contact_person" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
                </div>
                <div class="col-12 mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <button type="submit" name="add_supplier" class="btn btn-primary">Save Supplier</button>
        </form>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
