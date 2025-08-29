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
  <title>Products - Inventory Management</title>
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
      <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 content">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Products</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
          <i class="fa fa-plus"></i> Add Product
        </button>
      </div>

      <!-- Products Table -->
      <div class="card">
        <div class="card-body">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Name</th>
                <th>Stock</th>
                <th>Price</th>
                <th> </th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT p.product_id, p.name, p.stock, p.price, c.name AS category, s.name AS supplier
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.category_id
                      LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id";
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['product_id']}</td>
                              <td>{$row['category']}</td>
                              <td>{$row['supplier']}</td>
                              <td>{$row['name']}</td>
                              <td>{$row['stock']}</td>
                              <td>₱{$row['price']}</td>
                              <td>
                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editProductModal{$row['product_id']}'><i class='fa fa-edit'></i></button>
                                <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteProductModal{$row['product_id']}'><i class='fa fa-trash'></i></button>
                              </td>

                            </tr>";
                  }
              } else {
                  echo "<tr><td colspan='7' class='text-center'>No products found</td></tr>";
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
$result->data_seek(0); // reset result pointer
while ($row = $result->fetch_assoc()) {
?>
<!-- Edit Modal -->
<div class="modal fade" id="editProductModal<?php echo $row['product_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="update_product.php">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title">Edit Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">

          <!-- Product Name -->
          <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" required>
          </div>

          <!-- Category -->
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
              <option value="">-- Select Category --</option>
              <?php
              $catRes = $conn->query("SELECT * FROM categories");
              while ($cat = $catRes->fetch_assoc()) {
                $selected = ($cat['name'] == $row['category']) ? "selected" : "";
                echo "<option value='{$cat['category_id']}' $selected>{$cat['name']}</option>";
              }
              ?>
            </select>
          </div>

          <!-- Supplier -->
          <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select" required>
              <option value="">-- Select Supplier --</option>
              <?php
              $supRes = $conn->query("SELECT * FROM suppliers");
              while ($sup = $supRes->fetch_assoc()) {
                $selected = ($sup['name'] == $row['supplier']) ? "selected" : "";
                echo "<option value='{$sup['supplier_id']}' $selected>{$sup['name']}</option>";
              }
              ?>
            </select>
          </div>

          <!-- Stock -->
          <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" value="<?php echo $row['stock']; ?>" required>
          </div>

          <!-- Price -->
          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="text" class="form-control" name="price" value="<?php echo $row['price']; ?>" required>
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
<div class="modal fade" id="deleteProductModal<?php echo $row['product_id']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="delete_product.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Delete Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
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



<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="save_products.php">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                    <?php
                    $categories = $conn->query("SELECT * FROM categories");
                    while ($cat = $categories->fetch_assoc()) {
                        echo "<option value='{$cat['category_id']}'>{$cat['name']}</option>";
                    }
                    ?>
            </select>

          </div>
          <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select class="form-select" name="supplier_id" required>
              <?php
              $supResult = $conn->query("SELECT * FROM suppliers");
              while ($sup = $supResult->fetch_assoc()) {
                echo "<option value='{$sup['supplier_id']}'>{$sup['name']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="text" class="form-control" name="price" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
