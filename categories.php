<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'db.php'; ?>

<?php
// Handle new category submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories - Inventory Management</title>
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
        <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Categories</h2>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa fa-plus"></i> Add Category
          </button>
        </div>

        <!-- Categories Table -->
        <div class="card">
          <div class="card-body">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Category Name</th>
                  <th>Description</th>
                  <th> </th>
                </tr>
              </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM categories");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['category_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['description']}</td>
                                <td>
                                    <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editCategoryModal{$row['category_id']}'><i class='fa fa-edit'></i></button>
                                    <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteCategoryModal{$row['category_id']}'><i class='fa fa-trash'></i></button>
                                </td>

                                </tr>";
                        }
                    ?>
                </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

 
  <!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="categories.php">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary" name="add_category">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
?>
<!-- Edit Modal -->
<div class="modal fade" id="editCategoryModal<?php echo $row['category_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="update_category.php">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Edit Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="category_id" value="<?php echo $row['category_id']; ?>">
          <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required><?php echo $row['description']; ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteCategoryModal<?php echo $row['category_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="delete_category.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="category_id" value="<?php echo $row['category_id']; ?>">
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



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
